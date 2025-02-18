Vagrant.configure("2") do |config|
    config.vm.box = "ubuntu/jammy64"
    config.vm.hostname = "fullstack-dev-teguh"
  
    # Network Configuration
    config.vm.network "private_network", ip: "192.168.56.10"
  
    [
      { guest: 80, host: 8080 },    # HTTP
      { guest: 3000, host: 3000 },  # React
      { guest: 8765, host: 8765 }   # CakePHP
    ].each do |ports|
      config.vm.network "forwarded_port", 
        guest: ports[:guest], 
        host: ports[:host], 
        auto_correct: true
    end
  
    # VirtualBox Configuration
    config.vm.provider "virtualbox" do |vb|
      vb.memory = "4096"
      vb.cpus = 2
      vb.name = "fullstack-dev-teguh"
      vb.customize ["modifyvm", :id, "--natdnshostresolver1", "on"]
      vb.customize ["modifyvm", :id, "--ioapic", "on"]
    end
  
    # Synced Folders Configuration
    config.vm.synced_folder "./frontend", "/var/www/frontend", 
      owner: "vagrant", 
      group: "vagrant",
      mount_options: ["dmode=775,fmode=664"]
    config.vm.synced_folder "./backend", "/var/www/backend", 
      owner: "vagrant", 
      group: "vagrant",
      mount_options: ["dmode=775,fmode=664"]
  
    # Base provisioning script
    config.vm.provision "shell", path: "provision/setup.sh"
  
    # Setup npm and create startup script
    config.vm.provision "shell", privileged: false, path: "provision/npm-setup.sh"
  
    # Final message
    config.vm.provision "shell", path: "provision/final-message.sh"
  end
  