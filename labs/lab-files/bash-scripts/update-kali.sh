#!/bin/bash
apt-get update
apt-get dist-upgrade -y
apt-get autoremove -y
apt clean
