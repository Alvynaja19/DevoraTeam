#!/bin/bash
# ==============================================================
# DevoraV2 — Multipass Server Setup Script
# Ubuntu 22.04 — Jalankan sekali setelah launch instance
# ==============================================================
#
# Cara pakai:
#   1. Launch Multipass: multipass launch 22.04 --name devora --cpus 2 --memory 2G --disk 20G
#   2. Copy script:      multipass transfer scripts/multipass-setup.sh devora:/tmp/
#   3. Masuk ke VM:      multipass shell devora
#   4. Jalankan:         sudo bash /tmp/multipass-setup.sh
#
# ==============================================================

set -e  # exit on error

COLOR_GREEN='\033[0;32m'
COLOR_YELLOW='\033[1;33m'
COLOR_RED='\033[0;31m'
NC='\033[0m' # No Color

log()    { echo -e "${COLOR_GREEN}[✓] $1${NC}"; }
warn()   { echo -e "${COLOR_YELLOW}[!] $1${NC}"; }
error()  { echo -e "${COLOR_RED}[✗] $1${NC}"; exit 1; }

echo ""
echo "=============================================="
echo "  DevoraV2 — Multipass Server Setup"
echo "=============================================="
echo ""

# ----------------------------------------------
# 1. Update sistem
# ----------------------------------------------
log "Updating system packages..."
apt-get update -qq
apt-get upgrade -y -qq

# ----------------------------------------------
# 2. Install Docker Engine
# ----------------------------------------------
log "Installing Docker Engine..."
apt-get install -y -qq \
    ca-certificates \
    curl \
    gnupg \
    lsb-release

# Add Docker GPG key
install -m 0755 -d /etc/apt/keyrings
curl -fsSL https://download.docker.com/linux/ubuntu/gpg | \
    gpg --dearmor -o /etc/apt/keyrings/docker.gpg
chmod a+r /etc/apt/keyrings/docker.gpg

# Add Docker repo
echo \
  "deb [arch=$(dpkg --print-architecture) signed-by=/etc/apt/keyrings/docker.gpg] \
  https://download.docker.com/linux/ubuntu \
  $(. /etc/os-release && echo "$VERSION_CODENAME") stable" | \
  tee /etc/apt/sources.list.d/docker.list > /dev/null

apt-get update -qq
apt-get install -y -qq docker-ce docker-ce-cli containerd.io docker-buildx-plugin docker-compose-plugin

# Enable & start Docker
systemctl enable docker
systemctl start docker

log "Docker installed: $(docker --version)"
log "Docker Compose installed: $(docker compose version)"

# ----------------------------------------------
# 3. Add ubuntu user ke docker group
# ----------------------------------------------
log "Adding ubuntu user to docker group..."
usermod -aG docker ubuntu

# ----------------------------------------------
# 4. Setup Firewall (UFW)
# ----------------------------------------------
log "Setting up firewall..."
ufw --force reset
ufw default deny incoming
ufw default allow outgoing
ufw allow ssh         # port 22
ufw allow http        # port 80
ufw allow https       # port 443
ufw --force enable
log "Firewall configured: SSH + HTTP + HTTPS allowed"

# ----------------------------------------------
# 5. Setup direktori aplikasi
# ----------------------------------------------
log "Creating application directory..."
mkdir -p /opt/devora
chown ubuntu:ubuntu /opt/devora

# ----------------------------------------------
# 6. Install utilitas tambahan
# ----------------------------------------------
log "Installing additional utilities..."
apt-get install -y -qq \
    htop \
    curl \
    git \
    unzip \
    nano \
    net-tools

# ----------------------------------------------
# 7. Setup Docker log rotation
# ----------------------------------------------
log "Configuring Docker daemon..."
cat > /etc/docker/daemon.json << 'EOF'
{
    "log-driver": "json-file",
    "log-opts": {
        "max-size": "10m",
        "max-file": "3"
    }
}
EOF
systemctl restart docker

# ----------------------------------------------
# Selesai
# ----------------------------------------------
MULTIPASS_IP=$(hostname -I | awk '{print $1}')

echo ""
echo "=============================================="
echo -e "${COLOR_GREEN}  ✅ Setup selesai!"
echo ""
echo "  IP Server Anda: ${MULTIPASS_IP}"
echo ""
echo "  Langkah selanjutnya:"
echo "  1. cd /opt/devora"
echo "  2. Buat file .env (lihat .env.production.example)"
echo "  3. docker compose up -d"
echo "  4. Buka http://${MULTIPASS_IP} di browser"
echo -e "${NC}=============================================="
echo ""
