# SSH Key Setup Guide for Interserver Hosting

## Overview

This guide will help you set up SSH key-based authentication to connect to your Interserver shared hosting at `vda8100.is.cc`.

---

## Method 1: Using cPanel (Recommended for Beginners)

### Step 1.1: Generate SSH Key Pair in cPanel

1. Login to cPanel: https://vda8100.is.cc:2083
2. Go to **Security** → **SSH Access**
3. Click **Manage SSH Keys**
4. Click **Generate a New Key**
5. Enter the following:
   - **Key Name:** `id_rsa` (or any name you prefer)
   - **Key Password:** Create a strong password (remember this!)
   - **Key Type:** RSA
   - **Key Size:** 4096 (recommended)
6. Click **Generate Key**

### Step 1.2: Authorize the Public Key

1. In **Manage SSH Keys**, find your newly generated key
2. Click **Manage Authorization**
3. Click **Authorize**

### Step 1.3: Download the Private Key

1. In **Manage SSH Keys**, find your key under "Private Keys"
2. Click **View/Download** next to your key
3. Download the file (it will be named something like `id_rsa`)
4. Save it securely on your computer

### Step 1.4: Convert Key Format (If Needed)

If you're using Windows or PuTTY, you may need to convert the key:
- For PuTTY: Use **PuTTYgen** to convert `.pem` to `.ppk`
- For Linux/Mac: The key should work as-is

---

## Method 2: Using Your Local Computer (Linux/Mac)

### Step 2.1: Generate SSH Key Pair

Open your terminal and run:

```bash
# Generate SSH key pair
ssh-keygen -t rsa -b 4096 -C "your_email@example.com" -f ~/.ssh/id_inter_server

# You will be asked:
# - Enter passphrase: Create a strong password
# - Confirm passphrase: Re-enter the password

# Set correct permissions
chmod 700 ~/.ssh
chmod 600 ~/.ssh/id_inter_server
```

### Step 2.2: View Your Public Key

```bash
cat ~/.ssh/id_inter_server.pub
```

Copy this entire output (starts with `ssh-rsa AAAA...`)

### Step 2.3: Add Public Key to cPanel

1. Login to cPanel: https://vda8100.is.cc:2083
2. Go to **Security** → **SSH Access** → **Manage SSH Keys**
3. Click **Import Key**
4. Paste your public key into the "Public Key" field
5. Give it a name (e.g., `id_inter_server`)
6. Click **Import**

### Step 2.4: Authorize the Key

1. Go back to **Manage SSH Keys**
2. Find your imported key under "Authorized Keys"
3. Click **Manage Authorization**
4. Click **Authorize**

### Step 2.5: Connect Using the Private Key

```bash
# Connect using the private key
ssh -i ~/.ssh/id_inter_server location@vda8100.is.cc

# Or if SSH is on a different port
ssh -i ~/.ssh/id_inter_server -p 2222 location@vda8100.is.cc
```

---

## Method 3: Using PuTTY (Windows)

### Step 3.1: Download PuTTY

1. Download PuTTY from: https://www.chiark.greenend.org.uk/~sgtatham/putty/latest.html
2. You'll need:
   - PuTTY (main application)
   - PuTTYgen (key generator)
   - Pageant (SSH agent)

### Step 3.2: Generate Key with PuTTYgen

1. Open **PuTTYgen**
2. Select **RSA** with **4096** bits
3. Click **Generate**
4. Move your mouse randomly to generate the key
5. Enter a **Key passphrase**
6. Save both the **Public Key** and **Private Key** (.ppk file)

### Step 3.3: Copy Public Key

1. Copy the public key from PuTTYgen (the text in the box)
2. It starts with `ssh-rsa AAAA...`

### Step 3.4: Add Key to cPanel

1. Login to cPanel
2. Go to **Security** → **SSH Access** → **Manage SSH Keys**
3. Click **Import Key**
4. Paste the public key
5. Give it a name
6. Click **Import**
7. Authorize the key

### Step 3.5: Configure PuTTY

1. Open **PuTTY**
2. Go to **Connection** → **SSH** → **Auth**
3. Browse and select your **.ppk** file
4. Go to **Session**:
   - Host Name: `vda8100.is.cc`
   - Port: `2222` (Interserver uses this for SSH)
   - Connection type: SSH
5. Click **Open**
6. Login as: `location`

---

## Testing Your SSH Connection

After setting up keys, test the connection:

```bash
# Test from Linux/Mac
ssh -i ~/.ssh/id_inter_server -p 2222 location@vda8100.is.cc

# You should see:
# Enter passphrase for key '~/.ssh/id_inter_server':
# [Enter your passphrase]

# If successful, you'll see the server prompt:
# [location@vda8100 ~]$
```

---

## Setting Up SSH Config (Recommended)

Create a config file for easy connections:

```bash
# Edit/create config file
nano ~/.ssh/config
```

Add this content:

```
Host interserver
    HostName vda8100.is.cc
    User location
    Port 2222
    IdentityFile ~/.ssh/id_inter_server
    AddKeysToAgent yes
    UseKeychain yes
```

Now connect simply with:

```bash
ssh interserver
```

---

## Common Issues & Solutions

### Issue: "Permission denied (publickey)"

**Solution:** 
1. Verify the public key is authorized in cPanel
2. Make sure you're using the correct private key
3. Check that the key has correct permissions (`chmod 600`)

### Issue: "Connection refused"

**Solution:**
1. SSH might be on a different port
2. Try port `2222` instead of `22`
3. Check if SSH is enabled in cPanel

### Issue: "Could not open a connection to your authentication agent"

**Solution:**
```bash
eval "$(ssh-agent -s)"
ssh-add ~/.ssh/id_inter_server
```

### Issue: "Enter passphrase for key"

**Solution:**
1. Enter the passphrase you created when generating the key
2. Or use `ssh-agent` to cache the passphrase

---

## Security Best Practices

1. **Use a strong passphrase** - Minimum 12 characters
2. **Never share private keys** - Keep them on your computer only
3. **Use different keys for different servers** - Easier to revoke if compromised
4. **Backup your keys** - Store a secure copy
5. **Disable password authentication** - Once keys are working (optional)

---

## Quick Reference

| Item | Value |
|------|-------|
| Hostname | vda8100.is.cc |
| Username | location |
| SSH Port | 2222 |
| FTP Port | 21 |

---

## Next Steps After SSH Connection

Once connected via SSH, run these commands:

```bash
# Navigate to the application directory
cd /home/location/domains/locationshub.co.in/public_html/letters

# Set permissions
chmod 755 storage bootstrap/cache public/uploads

# Install dependencies
composer install --no-dev --optimize-autoloader

# Generate application key
php artisan key:generate

# Run migrations
php artisan migrate --force

# Create storage link
php artisan storage:link

# Set final permissions
chmod -R 775 storage bootstrap/cache
chmod 644 .env
```

---

## Need Help?

If you encounter issues:
1. Check Interserver documentation: https://www.interserver.net/tips/
2. Contact Interserver support
3. Verify SSH is enabled in your cPanel
