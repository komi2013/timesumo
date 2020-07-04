#!/bin/bash -e
cd /Project/timesumo
FILES=`/usr/bin/find ./ -amin -1`
KEY="/home/komatsu/.ssh/kagoya.key"
PATH="root@quigen.info:/tmp/"

# echo $FILES
for d in $FILES; do
    echo "${d}"
    F=`/bin/echo $d | /bin/sed 's/.\///'`
    echo $F $PATH$F
    /usr/bin/rsync -avzP -e "/usr/bin/ssh -i $KEY" $F $PATH$F
done