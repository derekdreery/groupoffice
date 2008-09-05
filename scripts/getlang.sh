
if [ "$2" = "" ]; then
 echo usage: $0 GOsource language
 exit
fi


if [ -d $2 ]; then
echo Directory $2 is in my way!
fi

cp -R $1 $2
cd $2
find . -type f ! \(  -name "$2.js" -o  -name "$2.inc.php" \) -exec rm -f {} \;
find -depth -type d -empty -exec rmdir {} \;

