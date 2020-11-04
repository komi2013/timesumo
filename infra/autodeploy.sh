#!/bin/bash -e
cd /Project/timesumo
while :
do
# FILES=`/usr/bin/find ./ -amin -1`
KEY="/home/komatsu/.ssh/kagoya.key"
PATH="root@quigen.info:/var/www/timesumo/"

# echo $FILES
# for d in $FILES; do
    # echo "${d}"
    # F=`/bin/echo $d | /bin/sed 's/.\///'`
    # echo $F $PATH$F
    # /usr/bin/rsync -avzPe "/usr/bin/ssh -i $KEY" $F $PATH$F
/usr/bin/rsync --exclude-from=./infra/exclude.txt -avzPe "/usr/bin/ssh -i $KEY" ./ $PATH
# done

done
