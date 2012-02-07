#!/bin/bash
cd ..
rm smartkeywording.rsp
tar -czv -f smartkeywording.rsp --exclude '*.git' smartkeywording_rs
