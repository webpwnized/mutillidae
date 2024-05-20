# Installation

I will describe almost full installation, starting with installing VirtualBox and creating and configuring VM, ending with accessing the working website.<br>
I will use Ubuntu 24.04.3 LTS as OS, Apache2 as web-server, php 8.2 and MariaDB as database server.<br>
My host OS is EndeavourOS. In case you use Windows, the only difference is in installing the VirtualBox. You can find Windows instructions on web and/or use any other hypervisor.<br>
Be sure, that virtualization is enabled in your BIOS settings! On AMD CPUs it is named as **AMD-V** and on Intel CPUs it is named as **Intel VT** (Intel Virtualization Technology). In case you don't want or can't enable virtualization on your host, you could **proceed with your host OS**, but this way **should be avoided in any ways!**

## Installing the VirtualBox

In case you use Arch linux too, the VirtualBox should be available via package manager. Just use `sudo pacman -S virtualbox virtualbox-guest-iso`.<br>
Not sure about other distros, so you can visit [official VirtualBox site](https://www.virtualbox.org/wiki/Linux_Downloads) and download required package. Then install it with `sudo dpkg -i downloaded_file.deb`, `sudo dnf install downloaded_file.rpm` or with another package manager from your system.

## Creating the VM

- Go to [Old Ubuntu Releases](https://old-releases.ubuntu.com/releases/) page and press **Ubuntu 22.04.3 LTS (Jammy Jellyfish)**;
- Find and click **ubuntu-22.04.3-live-server-amd64.iso** to download the installation iso file. It should be at the bottom of the page. Or you can just press [this link.](https://old-releases.ubuntu.com/releases/jammy/ubuntu-22.04.3-live-server-amd64.iso);
- Start VirtualBox. We would need only two buttons: **New (1)** and **Settings (2)**;

![main_virtualbox_window](.github/installation_screenshots/virtualbox_main.png)

- Press **New (1)** button. The new window would appear.

![first_configuration_window](.github/installation_screenshots/virtualbox_new_1.png)

1. Enter any conveninent name for new VM. It's up to you;
2. Choose the folder, where the subfolder with all required files for this VM would be created. Assume, that this path is "/home/user/VMs" and VM's name is "Test VM", all VM's files would be placed in "/home/user/VMs/Test VM" folder;
3. Choose downloaded previously iso installation image;
4. Check this box to disable automatic OS installation to configure it manually. Then press **Next** button.

![cpu_and_ram_configuration](.github/installation_screenshots/virtualbox_new_2.png)

- Configure RAM and CPUs availbale for VM. For our puproses it would be enough to set 1024MB RAM and 1 CPU. Press **Next**.

![disk_configuration](.github/installation_screenshots/virtualbox_new_3.png)

- Specify the VM's disk size. 10GB would be enough in our case. By default, the disk size would be minimal and would be expanding as needed. To allocate full size from the beginning check the corresponding checkbox. Then press **Next**.

![summary_configuration](.github/installation_screenshots/virtualbox_new_4.png)

- Here you could check all the configuration parameters. If everything is fine, then press **Finish** button and wait a bit, while VM is creating. It would be enough fast.
