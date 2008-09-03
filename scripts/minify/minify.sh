#!/bin/bash


if [ -z $2 ]; then
	cd `dirname "$0"`
	cd ../../www
else
	cd $2
fi


COMPRESSOR="/usr/lib/jvm/java-6-sun/bin/java -jar ../scripts/minify/yuicompressor-2.3.5/build/yuicompressor-2.3.5.jar"

#find . -name "*.js" -exec cat {} \; > go-all.js

echo Processing main scripts
#rm javascript/go-all.js
cat javascript/scripts.txt | xargs cat >> javascript/go-all.js 


if [ "$1" = "delete" ]; then
	for i in $(cat javascript/scripts.txt); do 
		rm $i;
	done
fi


$COMPRESSOR javascript/go-all.js -o javascript/go-all-min.js
rm javascript/go-all.js

for module in `ls modules`
do
    if [ -e modules/$module/scripts.txt  ]; then
		
		echo processing module $module
		
#		rm modules/$module/all-module-scripts.js

		for i in $(cat modules/$module/scripts.txt); do 
			cat $i >> modules/$module/all-module-scripts.js 
			echo ";" >> modules/$module/all-module-scripts.js
			
			if [ "$1" = "delete" ]; then
				rm $i;
			fi		
		done
		
		$COMPRESSOR modules/$module/all-module-scripts.js -o modules/$module/all-module-scripts-min.js
		
		rm modules/$module/all-module-scripts.js
	fi
done

echo Finished!
