#!/bin/bash
version=`sed -n 's/version: \(.*\)/\1/p' smartkeywording_rs.yaml `
echo "creating smart keywording plugin for version $version, press return key to continue ..."
read c
cd ..
rm smartkeywording*.rsp
tar -cz -f smartkeywording-$version.rsp --exclude '*.git' smartkeywording_rs