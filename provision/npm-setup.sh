#!/bin/bash
# Setup npm global directory
mkdir -p $HOME/.npm-global
npm config set prefix "$HOME/.npm-global"
echo 'export PATH="$HOME/.npm-global/bin:$PATH"' >> $HOME/.bashrc
export PATH="$HOME/.npm-global/bin:$PATH"
npm install -g npm@latest

# Install CakePHP dependencies
cd /var/www/backend
if [ -f "composer.json" ]; then
    echo "Installing CakePHP dependencies..."
    composer install
fi

# Install React dependencies
cd /var/www/frontend
if [ -f "package.json" ]; then
    echo "Installing React dependencies..."
    npm install
fi

# Create the startup script
mkdir -p $HOME/bin
cat > $HOME/bin/start-services.sh << 'EOF'
#!/bin/bash
export PATH="$HOME/.npm-global/bin:$PATH"

# Start CakePHP backend
cd /var/www/backend
chown -R vagrant:vagrant .
find . -type f -exec chmod 644 {} \;
find . -type d -exec chmod 755 {} \;
chmod +x bin/cake
php bin/cake.php server -H 0.0.0.0 -p 8765 &

# Start React frontend
cd /var/www/frontend
if [ -f "package.json" ]; then
  # Use the full path to node and react-scripts
  PORT=3000 HOST=0.0.0.0 node ./node_modules/.bin/react-scripts start &
fi
EOF

# Make script executable
chmod +x $HOME/bin/start-services.sh

# Add aliases to .bashrc
echo '# Development aliases' >> $HOME/.bashrc
echo 'alias start-dev="$HOME/bin/start-services.sh"' >> $HOME/.bashrc
echo 'alias stop-dev="pkill -f \"react-scripts\" && pkill -f \"php artisan\""' >> $HOME/.bashrc
