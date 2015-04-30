# -*- mode: ruby -*-
# vi: set ft=ruby :

# Vagrantfile API/syntax version. Don't touch unless you know what you're doing!
VAGRANTFILE_API_VERSION = "2"

Vagrant.configure(VAGRANTFILE_API_VERSION) do |config|
  # All Vagrant configuration is done here. The most common configuration
  # options are documented and commented below. For a complete reference,
  # please see the online documentation at vagrantup.com.

  config.vm.box = "ubuntu/trusty32"

  # The best Drupal compatible performance we can get seems to be an NFS share.
  # Image styles don't really work with rsync until rysnc supports
  # bi-directionality. The default file share with Virtual box is mad slow.
  # To avoid having to type your password on every vagrant up set up your
  # /etc/sudoers file as below:
  # @see https://docs.vagrantup.com/v2/synced-folders/nfs.html
  config.vm.network "private_network", ip: "192.168.50.10"
  config.vm.hostname = "local.example.com"
  config.vm.synced_folder ".", "/vagrant", :mount_options => ["dmode=777","fmode=777"]

  # Improve performance.
  # @see http://www.stefanwrobel.com/how-to-make-vagrant-performance-not-suck
  config.vm.provider "virtualbox" do |v|
    host = RbConfig::CONFIG['host_os']

    # Give VM 1/4 system memory & access to all cpu cores on the host
    if host =~ /darwin/
      cpus = `sysctl -n hw.ncpu`.to_i
      # sysctl returns Bytes and we need to convert to MB
      mem = `sysctl -n hw.memsize`.to_i / 1024 / 1024 / 4
    elsif host =~ /linux/
      # Edit made: Improve linux performance by only counting physical cores and not hyperthreads
      cpus = `grep "cpu cores" /proc/cpuinfo |sort -u |cut -d":" -f2`.to_i
      # meminfo shows KB and we need to convert to MB
      mem = `grep 'MemTotal' /proc/meminfo | sed -e 's/MemTotal://' -e 's/ kB//'`.to_i / 1024 / 4
    else # sorry Windows folks, I can't help you
      cpus = 2
      mem = 1024
    end

    v.customize ["modifyvm", :id, "--memory", mem]
    v.customize ["modifyvm", :id, "--cpus", cpus]
    v.customize ["modifyvm", :id, "--ioapic", "on"]
  end


  config.vm.provision "puppet" do |puppet|
    puppet.manifests_path = "puppet_manifests"
    puppet.manifest_file = "default.pp"
  end

end
