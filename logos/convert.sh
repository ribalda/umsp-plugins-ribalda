#!/bin/sh

for a in *
do
	convert "$a" -resize 60000@ conv/"$a".jpg
done
