#!/bin/bash

cd $1
pdflatex $2".tex"
pdflatex $2".tex"
rm $2".toc"
rm $2".log"
rm $2".aux"
