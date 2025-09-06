# Project: Deploy Automation

## 📋 System Requirements

### Minimum Specifications
- **Operating System**: CentOS/RHEL/Rocky Linux 9
- **Python Version**: 3.9+
- **Pre-installed Software (server)**: 
  - Docker 28.3.0, build 38b7060
  - python3 3.9.21
  - ansible core 2.15.13
  - git 2.47.1
- **Hardware Requirements**:
  - 2+ vCPU cores
  - 4+ GB RAM
  - 20+ GB storage

## Quick Start
#### Clone Project
```
git clone https://github.com/egorpivondepalo/vita-devops.git
cd vita-devops
```

#### Install Ansible Deps
```bash
ansible-galaxy collection install community.docker --force
```

#### Python Environment Setup
```bash
python -m venv venv
source venv/bin/activate
pip install -r requirements.txt
```


## Manual Run Playbook
- On local server
```bash
# DRY-RUN (generate files and deps on server)
ansible-playbook ansible/playbook.yml --tags dry-run

# Release (deploy to dev)
ansible-playbook ansible/playbook.yml --tags dev

# Release (deploy to release)
ansible-playbook ansible/playbook.yml --tags release

# PRUNE (stop all containers, clean working_dir and remove volumes)
ansible-playbook ansible/playbook.yml --tags prune
```



## **Automatic Deploy**
### **On local server**
- Run script `run_playbook.py` with params
```bash
# run on local server
python3 run playbook.py --dev --local

# run on remote server
python3 run_playbook.py --dev --host "remote_host_ip" --key "path_to_private_ssh_key"
```

### **On remote server**
- Run script for init environment
```bash
python3 run_playbook.py --host "192.168.0.170" --tag init
```

- Run script for deploy services
```bash
python3 run_playbook.py --host "192.168.0.170" --tag dev
```

**Arguments:**
    --no-check - skip check required environment

## ⚠️ Known Issues & Resolutions

### 1. Firewall Port Access Issue
**Problem**: Connection refused on http://127.0.0.1:1243

**Resolution**: Configure firewall rules
```bash
# Add port to firewall
firewall-cmd --add-port 1243/tcp --permanent
firewall-cmd --reload
```

**RESOLVED**: *ADDED TASK FOR ALLOW CUSTOM NGINX PORT!*

### 2. PHP-FPM Container Connection Drops
**Problem**: Container rejects incoming connections with error:
```
ERROR: Connection disallowed: IP address '172.18.0.X' has been dropped.
```

**Root Cause**: Misconfigured listen directive in PHP-FPM configuration

**Resolution**: Update PHP-FPM configuration in Dockerfile
```dockerfile
# Fix PHP-FPM configuration
RUN sed -i 's/^listen = .*/listen = 0.0.0.0:9000/' /usr/local/etc/php-fpm.d/www.conf && \
    sed -i 's/^;listen.allowed_clients/listen.allowed_clients = 0.0.0.0/' /usr/local/etc/php-fpm.d/www.conf
```

### 3. PHP-FPM Has No PSQL-drivers
**Problem**: PHP-FPM container can't connect to postgres database:
`PSLQ-driver not found`

**Resolution**: Update Dockerfile with php-fpm **(current solution)**:
```
FROM php:7.4-fpm

RUN sed -i 's/^listen = .*/listen = 0.0.0.0:9000/' /usr/local/etc/php-fpm.d/www.conf

RUN apt-get update && \
    apt-get install -y --no-install-recommends libpq-dev && \
    rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install pdo_pgsql pgsql
```

Or use container with pre-installed postgres drivers **(non-tested)**:
```
FROM bitnami/php-fpm:7.4

RUN sed -i 's/^listen = .*/listen = 0.0.0.0:9000/' /opt/bitnami/php/etc/php-fpm.conf
```
