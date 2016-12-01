<?php
// Generate a NRPE installation script based on https://support.nagios.com/kb/article.php?id=515

  // Allow it to be run from command line
  $osname = @trim(@strip_tags($argv['1']));
  $version = @trim(@strip_tags($argv['2']));
  $browser = 0;
  
  // If nothing done on command line, use _GET (browser mode)
  if (!isset($argv[0]) && $osname == '' && $version == '') {
		$osname = @trim(@strip_tags($_GET['osname']));
		$version = @trim(@strip_tags($_GET['version']));
		$browser = 1;
	}
	
	$s = array();

// Available Distros	
	$s['CentOS']['5.x'] = new stdClass;
	$s['CentOS']['6.x'] = new stdClass;
	$s['CentOS']['7.x'] = new stdClass;
	$s['RHEL']['5.x'] = new stdClass;
	$s['RHEL']['6.x'] = new stdClass;
	$s['RHEL']['7.x'] = new stdClass;
	$s['Oracle Linux']['5.x'] = new stdClass;
	$s['Oracle Linux']['6.x'] = new stdClass;
	$s['Oracle Linux']['7.x'] = new stdClass;
	$s['Ubuntu']['13.x 32-Bit'] = new stdClass;
	$s['Ubuntu']['13.x 64-Bit'] = new stdClass;
	$s['Ubuntu']['14.x 32-Bit'] = new stdClass;
	$s['Ubuntu']['14.x 64-Bit'] = new stdClass;
	$s['Ubuntu']['15.x 32-Bit'] = new stdClass;
	$s['Ubuntu']['15.x 64-Bit'] = new stdClass;
	$s['Ubuntu']['16.x 32-Bit'] = new stdClass;
	$s['Ubuntu']['16.x 64-Bit'] = new stdClass;
	$s['Fedora']['23'] = new stdClass;
	$s['SUSE SLES']['11.3'] = new stdClass;
	$s['SUSE SLES']['11.4'] = new stdClass;
	$s['SUSE SLES']['12'] = new stdClass;
	$s['SUSE SLES']['12.1'] = new stdClass;
	$s['openSUSE Leap']['42.1'] = new stdClass;
	$s['Solaris']['10'] = new stdClass;
	$s['Solaris']['11'] = new stdClass;
	$s['Debian']['7.x'] = new stdClass;
	$s['Debian']['8.x'] = new stdClass;
	$s['FreeBSD']['Any'] = new stdClass;
	$s['Apple OS X']['Any'] = new stdClass;

	if (isset($s[$osname][$version])) {

		// Prerequisites
		$s['CentOS']['5.x']->Prerequisites =
		$s['CentOS']['6.x']->Prerequisites =
		$s['CentOS']['7.x']->Prerequisites =
		$s['RHEL']['5.x']->Prerequisites =
		$s['RHEL']['6.x']->Prerequisites =
		$s['RHEL']['7.x']->Prerequisites =
		$s['Oracle Linux']['5.x']->Prerequisites =
		$s['Oracle Linux']['6.x']->Prerequisites =
		$s['Oracle Linux']['7.x']->Prerequisites =
		'yum install -y gcc glibc glibc-common openssl-devel perl wget';

		$s['Ubuntu']['13.x 32-Bit']->Prerequisites =
		$s['Ubuntu']['13.x 64-Bit']->Prerequisites =
		$s['Ubuntu']['14.x 32-Bit']->Prerequisites =
		$s['Ubuntu']['14.x 64-Bit']->Prerequisites =
		$s['Ubuntu']['15.x 32-Bit']->Prerequisites =
		$s['Ubuntu']['15.x 64-Bit']->Prerequisites =
		$s['Ubuntu']['16.x 32-Bit']->Prerequisites =
		$s['Ubuntu']['16.x 64-Bit']->Prerequisites =
		$s['Debian']['7.x']->Prerequisites =
		$s['Debian']['8.x']->Prerequisites ='apt-get update && apt-get install -y autoconf gcc libc6 libmcrypt-dev make libssl-dev wget';
			
		$s['Fedora']['23']->Prerequisites = 'dnf install -y gcc glibc glibc-common openssl-devel perl wget';
		
		$s['SUSE SLES']['11.3']->Prerequisites = "
			cd /tmp
			wget 'https://nu.novell.com/repo/$" . "RCE/SLE11-SDK-SP3-Pool/sle-11-x86_64/rpm/x86_64/sle-sdk-release-11.3-1.69.x86_64.rpm'
			wget 'https://nu.novell.com/repo/$" . "RCE/SLE11-SDK-SP3-Pool/sle-11-x86_64/rpm/x86_64/sle-sdk-release-SDK-11.3-1.69.x86_64.rpm'
			rpm -ivh sle-sdk-release-*
			suse_register
			zypper --non-interactive install autoconf gcc glibc libmcrypt-devel make libopenssl-devel wget
		";
		
		$s['SUSE SLES']['11.4']->Prerequisites = "
			cd /tmp
			wget 'https://nu.novell.com/repo/$" . "RCE/SLE11-SDK-SP4-Pool/sle-11-x86_64/rpm/x86_64/sle-sdk-release-11.4-1.55.x86_64.rpm'
			wget 'https://nu.novell.com/repo/$" . "RCE/SLE11-SDK-SP4-Pool/sle-11-x86_64/rpm/x86_64/sle-sdk-release-SDK-11.4-1.55.x86_64.rpm'
			rpm -ivh sle-sdk-release-*
			suse_register
			zypper --non-interactive install autoconf gcc glibc libmcrypt-devel make libopenssl-devel wget
		";
			
		$s['SUSE SLES']['12']->Prerequisites = "
			SUSEConnect -p sle-sdk/12/x86_64
			SUSEConnect -p sle-module-web-scripting/12/x86_64
			zypper --non-interactive install autoconf gcc glibc libmcrypt-devel make libopenssl-devel wget
		";
		
		$s['SUSE SLES']['12.1']->Prerequisites = "
			SUSEConnect -p sle-sdk/12.1/x86_64
			SUSEConnect -p sle-module-web-scripting/12/x86_64
			zypper --non-interactive install autoconf gcc glibc libmcrypt-devel make libopenssl-devel wget
		";
		
		$s['openSUSE Leap']['42.1']->Prerequisites = 'sudo zypper --non-interactive install autoconf gcc glibc libmcrypt-devel make libopenssl-devel wget';
		
		$s['Solaris']['10']->Prerequisites = "
			echo 'PATH=/usr/sfw/bin:/usr/ccs/bin:/opt/csw/bin:$" . "PATH' >> $" . "HOME/.profile
			echo 'export PATH' >> $" . "HOME/.profile
			. $" . "HOME/.profile
			pkgadd -d http://get.opencsw.org/now
			answer all
			answer y
			perl -ni.bak -le 'print; print \"mirror=http://mirrors.ibiblio.org/opencsw/stable\" if /mirror=/' /etc/opt/csw/pkgutil.conf
			pkgutil -i autoconf
			answer y for remaining questions
			pkgutil -i automake
			answer y for remaining questions
		";
		
		$s['Solaris']['11']->Prerequisites = "
			echo 'export PATH=$" . "PATH:/opt/csw/bin:/usr/xpg4/bin:/usr/sfw/bin' >> ~/.profile
			source ~/.profile
			pkgadd -d http://get.opencsw.org/now
			answer all
			answer y
			perl -ni.bak -le 'print; print \"mirror=http://mirrors.ibiblio.org/opencsw/stable\" if /mirror=/' /etc/opt/csw/pkgutil.conf
			pkgutil -i autoconf
			answer y for remaining questions
			pkgutil -i automake
			answer y for remaining questions
			pkg install gcc-45
		";
		
		$s['FreeBSD']['Any']->Prerequisites = "
			portsnap fetch update
			cd /usr/ports/ftp/wget
			make install clean
			answer any questions / prompts, multiple packages may be installed
			cd /usr/ports/devel/autoconf
			make install clean
			answer any questions / prompts, multiple packages may be installed
			cd /usr/ports/devel/automake
			make install clean
			answer any questions / prompts, multiple packages may be installed
			rehash
		";
		
		$s['Apple OS X']['Any']->Prerequisites = "
			xcodebuild -license
			View the agreement and then type agree
			/usr/bin/ruby -e \"$(curl -fsSL https://raw.githubusercontent.com/Homebrew/install/master/install)\"
			Press Return to continue
			brew install openssl
			brew link openssl --force
		";
		
		
	// Downloading the Source

		$s['CentOS']['5.x']->Download =
		$s['CentOS']['6.x']->Download =
		$s['CentOS']['7.x']->Download =
		$s['RHEL']['5.x']->Download =
		$s['RHEL']['6.x']->Download =
		$s['RHEL']['7.x']->Download =
		$s['Oracle Linux']['5.x']->Download =
		$s['Oracle Linux']['6.x']->Download =
		$s['Oracle Linux']['7.x']->Download =
		$s['Ubuntu']['13.x 32-Bit']->Download =
		$s['Ubuntu']['13.x 64-Bit']->Download =
		$s['Ubuntu']['14.x 32-Bit']->Download =
		$s['Ubuntu']['14.x 64-Bit']->Download =
		$s['Ubuntu']['15.x 32-Bit']->Download =
		$s['Ubuntu']['15.x 64-Bit']->Download =
		$s['Ubuntu']['16.x 32-Bit']->Download =
		$s['Ubuntu']['16.x 64-Bit']->Download =
		$s['Fedora']['23']->Download =
		$s['SUSE SLES']['11.3']->Download =
		$s['SUSE SLES']['11.4']->Download =
		$s['SUSE SLES']['12']->Download =
		$s['SUSE SLES']['12.1']->Download =
		$s['openSUSE Leap']['42.1']->Download =
		$s['Debian']['7.x']->Download =
		$s['Debian']['8.x']->Download =
		$s['FreeBSD']['Any']->Download =
		"
			cd /tmp
			wget --no-check-certificate https://github.com/NagiosEnterprises/nrpe/archive/3.0.tar.gz
			tar xzf 3*
		";
		
		/*
			First, make sure Xcode is installed. If it is not installed visit the App Store and install Xcode (3.8GB download).
		*/
		$s['Apple OS X']['Any']->Download =
		"
			cd /tmp
			curl -L -o nrpe.tar.gz https://github.com/NagiosEnterprises/nrpe/archive/3.0.tar.gz
			tar xzf 3*
		";
		
		
		$s['Solaris']['10']->Download =
		$s['Solaris']['11']->Download =
		"
			cd /tmp
			wget --no-check-certificate https://github.com/NagiosEnterprises/nrpe/archive/3.0.tar.gz
			gunzip -c 3.0.tar.gz | tar -xf -
		";


	// Compile

		/*
			Note that if you want to pass arguments through NRPE you must specify this in the
			configuration option as indicated below. If you prefer to you can omit the
			--enable-command-args flag. Removing this flag will require that all arguments be
			explicitly set in the nrpe.cfg file on each server monitored.
			
			--enable-command-args is required for NEMS.
		*/
		
		$s['CentOS']['5.x']->Compile =
		$s['CentOS']['6.x']->Compile =
		$s['CentOS']['7.x']->Compile =
		$s['RHEL']['5.x']->Compile =
		$s['RHEL']['6.x']->Compile =
		$s['RHEL']['7.x']->Compile =
		$s['Fedora']['23']->Compile =
		$s['Oracle Linux']['5.x']->Compile =
		$s['Oracle Linux']['6.x']->Compile =
		$s['Oracle Linux']['7.x']->Compile =
		$s['Debian']['7.x']->Compile =
		$s['Debian']['8.x']->Compile =
		$s['FreeBSD']['Any']->Compile =
		$s['Apple OS X']['Any']->Compile =
		"
			cd /tmp/nrpe-3.0/
			./configure --enable-command-args
			make all
		";
		
		$s['Ubuntu']['13.x 32-Bit']->Compile =
		$s['Ubuntu']['14.x 32-Bit']->Compile =
		$s['Ubuntu']['15.x 32-Bit']->Compile =
		$s['Ubuntu']['16.x 32-Bit']->Compile =
		"
			cd /tmp/nrpe-3.0/
			./configure --enable-command-args --with-ssl-lib=/usr/lib/i386-linux-gnu/
			make all
		";
		
		$s['Ubuntu']['13.x 64-Bit']->Compile =
		$s['Ubuntu']['14.x 64-Bit']->Compile =
		$s['Ubuntu']['15.x 64-Bit']->Compile =
		$s['Ubuntu']['16.x 64-Bit']->Compile =
		"
			cd /tmp/nrpe-3.0/
			./configure --enable-command-args --with-ssl-lib=/usr/lib/x86_64-linux-gnu/
			make all
		";
		
		$s['SUSE SLES']['11.3']->Compile =
		$s['SUSE SLES']['11.4']->Compile =
		$s['SUSE SLES']['12']->Compile =
		$s['SUSE SLES']['12.1']->Compile =
		$s['openSUSE Leap']['42.1']->Compile =
		"
			cd /tmp/nrpe-3.0/
			./configure --enable-command-args
			make all
		";
		
		$s['Solaris']['10']->Compile =
		$s['Solaris']['11']->Compile =
		"
			cd /tmp/nrpe-3.0/
			./configure --enable-command-args
			gmake all
		";


	// Create User And Group
		
		/*
			This creates the nagios user and group.
		*/
		
		$s['CentOS']['5.x']->User =
		$s['CentOS']['6.x']->User =
		$s['CentOS']['7.x']->User =
		$s['RHEL']['5.x']->User =
		$s['RHEL']['6.x']->User =
		$s['RHEL']['7.x']->User =
		$s['Oracle Linux']['5.x']->User =
		$s['Oracle Linux']['6.x']->User =
		$s['Oracle Linux']['7.x']->User =
		$s['Fedora']['23']->User =
		$s['Debian']['7.x']->User =
		$s['Debian']['8.x']->User =
		$s['FreeBSD']['Any']->User =
		$s['Ubuntu']['13.x 32-Bit']->User =
		$s['Ubuntu']['13.x 64-Bit']->User =
		$s['Ubuntu']['14.x 32-Bit']->User =
		$s['Ubuntu']['14.x 64-Bit']->User =
		$s['Ubuntu']['15.x 32-Bit']->User =
		$s['Ubuntu']['15.x 64-Bit']->User =
		$s['Ubuntu']['16.x 32-Bit']->User =
		$s['Ubuntu']['16.x 64-Bit']->User =
		$s['SUSE SLES']['11.3']->User =
		$s['SUSE SLES']['11.4']->User =
		$s['SUSE SLES']['12']->User =
		$s['SUSE SLES']['12.1']->User =
		$s['openSUSE Leap']['42.1']->User =
		$s['Apple OS X']['Any']->User =
		"make install-groups-users";
		
		$s['Solaris']['10']->User =
		$s['Solaris']['11']->User =
		"gmake install-groups-users";


	// Install Binaries
		/*
			This step installs the binary files, the NRPE daemon and the check_nrpe plugin.

			If you only wanted to install the daemon, run the command make install-daemon
			instead of the command below. However it is useful having the check_nrpe plugin
			installed for testing purposes.

			If you only wanted to install the check_nrpe plugin, refer to the section at the
			bottom of this KB article as there a lot of steps that can be skipped. Installing
			only the plugin is usually done on your Nagios server and workers.
		*/

		$s['CentOS']['5.x']->Install =
		$s['CentOS']['6.x']->Install =
		$s['CentOS']['7.x']->Install =
		$s['RHEL']['5.x']->Install =
		$s['RHEL']['6.x']->Install =
		$s['RHEL']['7.x']->Install =
		$s['Oracle Linux']['5.x']->Install =
		$s['Oracle Linux']['6.x']->Install =
		$s['Oracle Linux']['7.x']->Install =
		$s['Ubuntu']['13.x 32-Bit']->Install =
		$s['Ubuntu']['13.x 64-Bit']->Install =
		$s['Ubuntu']['14.x 32-Bit']->Install =
		$s['Ubuntu']['14.x 64-Bit']->Install =
		$s['Ubuntu']['15.x 32-Bit']->Install =
		$s['Ubuntu']['15.x 64-Bit']->Install =
		$s['Ubuntu']['16.x 32-Bit']->Install =
		$s['Ubuntu']['16.x 64-Bit']->Install =
		$s['Fedora']['23']->Install =
		$s['SUSE SLES']['11.3']->Install =
		$s['SUSE SLES']['11.4']->Install =
		$s['SUSE SLES']['12']->Install =
		$s['SUSE SLES']['12.1']->Install =
		$s['openSUSE Leap']['42.1']->Install =
		$s['Debian']['7.x']->Install =
		$s['Debian']['8.x']->Install =
		$s['FreeBSD']['Any']->Install =
		$s['Apple OS X']['Any']->Install =
		"make install";
		
		$s['Solaris']['10']->Install =
		$s['Solaris']['11']->Install =
		"gmake install";


	// Install Configuration Files

		$s['CentOS']['5.x']->Config =
		$s['CentOS']['6.x']->Config =
		$s['CentOS']['7.x']->Config =
		$s['RHEL']['5.x']->Config =
		$s['RHEL']['6.x']->Config =
		$s['RHEL']['7.x']->Config =
		$s['Oracle Linux']['5.x']->Config =
		$s['Oracle Linux']['6.x']->Config =
		$s['Oracle Linux']['7.x']->Config =
		$s['Ubuntu']['13.x 32-Bit']->Config =
		$s['Ubuntu']['13.x 64-Bit']->Config =
		$s['Ubuntu']['14.x 32-Bit']->Config =
		$s['Ubuntu']['14.x 64-Bit']->Config =
		$s['Ubuntu']['15.x 32-Bit']->Config =
		$s['Ubuntu']['15.x 64-Bit']->Config =
		$s['Ubuntu']['16.x 32-Bit']->Config =
		$s['Ubuntu']['16.x 64-Bit']->Config =
		$s['Fedora']['23']->Config =
		$s['SUSE SLES']['11.3']->Config =
		$s['SUSE SLES']['11.4']->Config =
		$s['SUSE SLES']['12']->Config =
		$s['SUSE SLES']['12.1']->Config =
		$s['openSUSE Leap']['42.1']->Config =
		$s['Debian']['7.x']->Config =
		$s['Debian']['8.x']->Config =
		$s['FreeBSD']['Any']->Config =
		$s['Apple OS X']['Any']->Config =
		"make install-config";
		
		$s['Solaris']['10']->Config =
		$s['Solaris']['11']->Config =
		"gmake install-config";
		
		
	// Update Services File

		/*
			The /etc/services file is used by applications to translate human readable service names
			into port numbers when connecting to a machine across a network.
		*/
		

		$s['CentOS']['5.x']->ServicesFile =
		$s['CentOS']['6.x']->ServicesFile =
		$s['CentOS']['7.x']->ServicesFile =
		$s['RHEL']['5.x']->ServicesFile =
		$s['RHEL']['6.x']->ServicesFile =
		$s['RHEL']['7.x']->ServicesFile =
		$s['Oracle Linux']['5.x']->ServicesFile =
		$s['Oracle Linux']['6.x']->ServicesFile =
		$s['Oracle Linux']['7.x']->ServicesFile =
		$s['Fedora']['23']->ServicesFile =
		$s['Debian']['7.x']->ServicesFile =
		$s['Debian']['8.x']->ServicesFile =
		$s['FreeBSD']['Any']->ServicesFile =
		"
			echo >> /etc/services
			echo '# Nagios services' >> /etc/services
			echo 'nrpe    5666/tcp' >> /etc/services
		";
		
		$s['Ubuntu']['13.x 32-Bit']->ServicesFile =
		$s['Ubuntu']['13.x 64-Bit']->ServicesFile =
		$s['Ubuntu']['14.x 32-Bit']->ServicesFile =
		$s['Ubuntu']['14.x 64-Bit']->ServicesFile =
		$s['Ubuntu']['15.x 32-Bit']->ServicesFile =
		$s['Ubuntu']['15.x 64-Bit']->ServicesFile =
		$s['Ubuntu']['16.x 32-Bit']->ServicesFile =
		$s['Ubuntu']['16.x 64-Bit']->ServicesFile =
		$s['SUSE SLES']['11.3']->ServicesFile =
		$s['SUSE SLES']['11.4']->ServicesFile =
		$s['SUSE SLES']['12']->ServicesFile =
		$s['SUSE SLES']['12.1']->ServicesFile =
		$s['openSUSE Leap']['42.1']->ServicesFile =
		$s['Apple OS X']['Any']->ServicesFile =
		"
			sh -c \"echo >> /etc/services\"
			sh -c \"sudo echo '# Nagios services' >> /etc/services\"
			sh -c \"sudo echo 'nrpe    5666/tcp' >> /etc/services\"
		";
		

	// Install Service / Daemon

		$s['CentOS']['5.x']->Install =
		$s['CentOS']['6.x']->Install =
		$s['RHEL']['5.x']->Install =
		$s['RHEL']['6.x']->Install =
		$s['FreeBSD']['Any']->Install =
		$s['Oracle Linux']['5.x']->Install =
		$s['Oracle Linux']['6.x']->Install =
		"make install-init";
		
		$s['CentOS']['7.x']->Install =
		$s['RHEL']['7.x']->Install =
		$s['Oracle Linux']['7.x']->Install =
		$s['Fedora']['23']->Install =
		"
			make install-init
			systemctl enable nrpe.service
		";

		$s['SUSE SLES']['11.3']->Install =
		$s['SUSE SLES']['11.4']->Install =
		"
			make install-init
			/sbin/chkconfig --set nrpe on
		";
		
		$s['SUSE SLES']['12']->Install =
		$s['SUSE SLES']['12.1']->Install =
		$s['openSUSE Leap']['42.1']->Install =
		"
			make install-init
			systemctl enable nrpe.service
		";

		$s['Ubuntu']['13.x 32-Bit']->Install =
		$s['Ubuntu']['13.x 64-Bit']->Install =
		$s['Ubuntu']['14.x 32-Bit']->Install =
		$s['Ubuntu']['14.x 64-Bit']->Install =
		"make install-init";
		
		$s['Ubuntu']['15.x 32-Bit']->Install =
		$s['Ubuntu']['15.x 64-Bit']->Install =
		$s['Ubuntu']['16.x 32-Bit']->Install =
		$s['Ubuntu']['16.x 64-Bit']->Install =
		"
			make install-init
			systemctl enable nrpe.service
		";
		
		$s['Solaris']['10']->Install =
		$s['Solaris']['11']->Install =
		"gmake install-init";
		
		$s['Debian']['7.x']->Install =
		"
			make install-init
			update-rc.d nrpe defaults
		";
		
		$s['Debian']['8.x']->Install =
		"
			make install-init
			systemctl enable nrpe.service
		";
		
		$s['Apple OS X']['Any']->Install = "make install-init";

		
	// Configure Firewall

		/*
			Port 5666 is used by NRPE and needs to be opened on the local firewall.
		*/
		
		$s['CentOS']['5.x']->Firewall =
		$s['CentOS']['6.x']->Firewall =
		$s['RHEL']['5.x']->Firewall =
		$s['RHEL']['6.x']->Firewall =
		$s['Oracle Linux']['5.x']->Firewall =
		$s['Oracle Linux']['6.x']->Firewall =
		"
			iptables -I INPUT -p tcp --destination-port 5666 -j ACCEPT
			service iptables save
		";
		
		$s['CentOS']['7.x']->Firewall =
		$s['RHEL']['7.x']->Firewall =
		$s['Oracle Linux']['7.x']->Firewall =
		"
			firewall-cmd --zone=public --add-port=5666/tcp
			firewall-cmd --zone=public --add-port=5666/tcp --permanent
		";

		$s['Fedora']['23']->Firewall =
		"
			firewall-cmd --zone=FedoraServer --add-port=5666/tcp
			firewall-cmd --zone=FedoraServer --add-port=5666/tcp --permanent
		";
		
		$s['Ubuntu']['13.x 32-Bit']->Firewall =
		$s['Ubuntu']['13.x 64-Bit']->Firewall =
		$s['Ubuntu']['14.x 32-Bit']->Firewall =
		$s['Ubuntu']['14.x 64-Bit']->Firewall =
		$s['Ubuntu']['15.x 32-Bit']->Firewall =
		$s['Ubuntu']['15.x 64-Bit']->Firewall =
		$s['Ubuntu']['16.x 32-Bit']->Firewall =
		$s['Ubuntu']['16.x 64-Bit']->Firewall =
		"
			mkdir -p /etc/ufw/applications.d
			sh -c \"echo '[NRPE]' > /etc/ufw/applications.d/nagios\"
			sh -c \"echo 'title=Nagios Remote Plugin Executor' >> /etc/ufw/applications.d/nagios\"
			sh -c \"echo 'description=Allows remote execution of Nagios plugins' >> /etc/ufw/applications.d/nagios\"
			sh -c \"echo 'ports=5666/tcp' >> /etc/ufw/applications.d/nagios\"
			ufw allow NRPE
			ufw reload
		";
		
		$s['SUSE SLES']['11.3']->Firewall =
		$s['SUSE SLES']['11.4']->Firewall =
		"
			sed -i '/FW_SERVICES_EXT_TCP=/s/\"$/\ 5666\"/' /etc/sysconfig/SuSEfirewall2
			/sbin/service SuSEfirewall2_init restart
			/sbin/service SuSEfirewall2_setup restart
		";
		
		$s['SUSE SLES']['12']->Firewall =
		$s['SUSE SLES']['12.1']->Firewall =
		$s['openSUSE Leap']['42.1']->Firewall =
		"
			sed -i '/FW_SERVICES_EXT_TCP=/s/\"$/\ 5666\"/' /etc/sysconfig/SuSEfirewall2
			systemctl restart SuSEfirewall2
		";

		$s['Debian']['7.x']->Firewall =
		$s['Debian']['8.x']->Firewall =
		"
			iptables -I INPUT -p tcp --destination-port 5666 -j ACCEPT
			apt-get install iptables-persistent
		";
		
		/*
			Solaris
			On a manually networked system, IP Filter is not enabled by default. Please refer to the
			Solaris documentation for information on how to enable or configure IP Filter to allow
			TCP port 5666 inbound.
			https://docs.oracle.com/cd/E26502_01/html/E28990/ipfad-10.html

			FreeBSD
			Please refer to the FreeBSD documentation for information on how to enable or configure IP
			Filter to allow TCP port 5666 inbound.
			https://www.freebsd.org/doc/handbook/firewalls.html

			Apple OS X
			The firewall in OS X is turned off by default. Please refer to the Apple documentation for
			information on how to enable or configure TCP port 5666 inbound.

		*/
		


	// Update Configuration File

		/*
		
			The file nrpe.cfg is where the following settings will be defined. It is located:

			CentOS | RHEL | Ubuntu | Fedora | SUSE SLES | openSUSE | Solaris | Oracle Linux | FreeBSD

			/usr/local/nagios/etc/nrpe.cfg
			 
			allowed_hosts=

			At this point NRPE will only listen to requests from itself (127.0.0.1). If you wanted your nagios
			server to be able to connect, add it's IP address after a comma (in this example it's 10.25.5.2):

			allowed_hosts=127.0.0.1,10.25.5.2
			 
			dont_blame_nrpe=

			This option determines whether or not the NRPE daemon will allow clients to specify arguments to
			commands that are executed. We are going to allow this, as it enables more advanced NPRE configurations.

			dont_blame_nrpe=1 

			The following commands make the configuration changes described above.
			
		*/

		if ($browser == 1) {
			$NEMS_IP = $_SERVER['SERVER_ADDR'];
		} else {
			$NEMS_IP = trim(`hostname -I`);
		}
		

		$s['CentOS']['5.x']->NRPEcfg =
		$s['CentOS']['6.x']->NRPEcfg =
		$s['CentOS']['7.x']->NRPEcfg =
		$s['RHEL']['5.x']->NRPEcfg =
		$s['RHEL']['6.x']->NRPEcfg =
		$s['RHEL']['7.x']->NRPEcfg =
		$s['Oracle Linux']['5.x']->NRPEcfg =
		$s['Oracle Linux']['6.x']->NRPEcfg =
		$s['Oracle Linux']['7.x']->NRPEcfg =
		$s['Fedora']['23']->NRPEcfg =
		$s['Debian']['7.x']->NRPEcfg =
		$s['Debian']['8.x']->NRPEcfg =
		$s['Ubuntu']['13.x 32-Bit']->NRPEcfg =
		$s['Ubuntu']['13.x 64-Bit']->NRPEcfg =
		$s['Ubuntu']['14.x 32-Bit']->NRPEcfg =
		$s['Ubuntu']['14.x 64-Bit']->NRPEcfg =
		$s['Ubuntu']['15.x 32-Bit']->NRPEcfg =
		$s['Ubuntu']['15.x 64-Bit']->NRPEcfg =
		$s['Ubuntu']['16.x 32-Bit']->NRPEcfg =
		$s['Ubuntu']['16.x 64-Bit']->NRPEcfg =
		$s['SUSE SLES']['11.3']->NRPEcfg =
		$s['SUSE SLES']['11.4']->NRPEcfg =
		$s['SUSE SLES']['12']->NRPEcfg =
		$s['SUSE SLES']['12.1']->NRPEcfg =
		$s['openSUSE Leap']['42.1']->NRPEcfg =
		"
			sed -i '/^allowed_hosts=/s/$/,$NEMS_IP/' /usr/local/nagios/etc/nrpe.cfg
			sed -i 's/^dont_blame_nrpe=.*/dont_blame_nrpe=1/g' /usr/local/nagios/etc/nrpe.cfg
		";
		
		$s['FreeBSD']['Any']->NRPEcfg =
		"
			sed -i '' '/^allowed_hosts=/s/$/,$NEMS_IP/' /usr/local/nagios/etc/nrpe.cfg
			sed -i '' 's/^dont_blame_nrpe=.*/dont_blame_nrpe=1/g' /usr/local/nagios/etc/nrpe.cfg
		";
		
		$s['Solaris']['10']->NRPEcfg =
		$s['Solaris']['11']->NRPEcfg =
		"
			perl -ni -le '$" . "output=$" . "_; $" . "output.=\",$NEMS_IP\" if /^allowed_hosts=/; print $" . "output' /usr/local/nagios/etc/nrpe.cfg
			perl -p -i -e 's/^dont_blame_nrpe=.*/dont_blame_nrpe=1/g' /usr/local/nagios/etc/nrpe.cfg
		";
		
		$s['Apple OS X']['Any']->NRPEcfg =
		"
			sudo sed -i '' '/^allowed_hosts=/s/$/,$NEMS_IP/' /usr/local/nagios/etc/nrpe.cfg
			sudo sed -i '' 's/^dont_blame_nrpe=.*/dont_blame_nrpe=1/g' /usr/local/nagios/etc/nrpe.cfg
		";
		
		
	// Start Service / Daemon

		$s['CentOS']['5.x']->Daemon =
		$s['RHEL']['5.x']->Daemon =
		$s['Oracle Linux']['5.x']->Daemon =
		$s['Debian']['7.x']->Daemon =
		$s['FreeBSD']['Any']->Daemon =
		"service nrpe start";
		
		$s['CentOS']['6.x']->Daemon =
		$s['RHEL']['6.x']->Daemon =
		$s['Oracle Linux']['6.x']->Daemon =
		$s['Ubuntu']['13.x 32-Bit']->Daemon =
		$s['Ubuntu']['13.x 64-Bit']->Daemon =
		$s['Ubuntu']['14.x 32-Bit']->Daemon =
		$s['Ubuntu']['14.x 64-Bit']->Daemon =
		"start nrpe";
		
		$s['CentOS']['7.x']->Daemon =
		$s['RHEL']['7.x']->Daemon =
		$s['Oracle Linux']['7.x']->Daemon =
		$s['Fedora']['23']->Daemon =
		$s['Debian']['8.x']->Daemon =
		$s['SUSE SLES']['12']->Daemon =
		$s['SUSE SLES']['12.1']->Daemon =
		$s['openSUSE Leap']['42.1']->Daemon =
		$s['Ubuntu']['15.x 32-Bit']->Daemon =
		$s['Ubuntu']['15.x 64-Bit']->Daemon =
		$s['Ubuntu']['16.x 32-Bit']->Daemon =
		$s['Ubuntu']['16.x 64-Bit']->Daemon =
		"systemctl start nrpe.service";

		$s['SUSE SLES']['11.3']->Daemon =
		$s['SUSE SLES']['11.4']->Daemon =
		"/sbin/service nrpe start";
		
		$s['Solaris']['10']->Daemon =
		$s['Solaris']['11']->Daemon =
		"svcadm enable nrpe";
		
		$s['Apple OS X']['Any']->Daemon =
		"launchctl start org.nagios.nrpe";
		
	/*

		$s['CentOS']['5.x']-> =
		$s['CentOS']['6.x']-> =
		$s['CentOS']['7.x']-> =
		$s['RHEL']['5.x']-> =
		$s['RHEL']['6.x']-> =
		$s['RHEL']['7.x']-> =
		$s['Oracle Linux']['5.x']-> =
		$s['Oracle Linux']['6.x']-> =
		$s['Oracle Linux']['7.x']-> =
		$s['Ubuntu']['13.x 32-Bit']-> =
		$s['Ubuntu']['13.x 64-Bit']-> =
		$s['Ubuntu']['14.x 32-Bit']-> =
		$s['Ubuntu']['14.x 64-Bit']-> =
		$s['Ubuntu']['15.x 32-Bit']-> =
		$s['Ubuntu']['15.x 64-Bit']-> =
		$s['Ubuntu']['16.x 32-Bit']-> =
		$s['Ubuntu']['16.x 64-Bit']-> =
		$s['Fedora']['23']-> =
		$s['SUSE SLES']['11.3']-> =
		$s['SUSE SLES']['11.4']-> =
		$s['SUSE SLES']['12']-> =
		$s['SUSE SLES']['12.1']-> =
		$s['openSUSE Leap']['42.1']-> =
		$s['Solaris']['10']-> =
		$s['Solaris']['11']-> =
		$s['Debian']['7.x']-> =
		$s['Debian']['8.x']-> =
		$s['FreeBSD']['Any']-> =
		$s['Apple OS X']['Any']-> =
		
	*/
	
	$script = '#!/bin/bash';
	$script .= '
if [[ $EUID -ne 0 ]]; then
  echo "ERROR: You must be a root" 2>&1
  exit 1
else
';
foreach ($s[$osname][$version] as $task => $commands) {
	$script .= '# ' . $task . PHP_EOL;
	$script .= $commands . PHP_EOL;
}
	$script .= '
fi
	';
		echo $script;
	}	else {
		echo PHP_EOL;
		echo 'Invalid OSNAME / VERSION Combination' . PHP_EOL . PHP_EOL;
		if ($browser == 1) echo '<br /><br />';
		echo 'Available OSNAME/VERSION:' . PHP_EOL;
		if ($browser == 1) echo '<br />';
		foreach ($s as $osname=>$data) {
			foreach ($data as $version => $null) {
				echo ' OSNAME: "' . $osname . '" VERSION: "' . $version . '"' . PHP_EOL;
				if ($browser == 1) echo '<br />';
			}
		}
		echo PHP_EOL;
		if ($browser == 1) echo '<br />';
		echo 'Usage: php ' . $argv[0] . ' "OSNAME" ' . '"VERSION"' . PHP_EOL;
		
		echo PHP_EOL;
		exit();
	}

?>
