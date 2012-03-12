#!/bin/bash
# get current commit
git log | grep -m 1 commit
commit=`git log | grep -m 1 commit `
commitnick=${commit:7:10}
echo "updating version information in smartkeywording_rs.yaml with latest git commit $commitnick"
sed -ibk "s/version: .*/version: $commitnick/" smartkeywording_rs.yaml
rm smartkeywording_rs.yamlbk
cd ..
rm smartkeywording*.rsp
tar -cz -f smartkeywording-$commitnick.rsp --exclude '*.git' smartkeywording_rs
