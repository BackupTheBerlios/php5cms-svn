#!/bin/sh

find $1 - type d -name "__example" | 
xargs rm -rf
