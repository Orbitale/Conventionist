#!/bin/bash

DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"

cd ${DIR}/../

# Colors used in Imagemagick must be names, not rgb or hsl.
# For all available colors:
# @see https://imagemagick.org/script/color.php#color_names
colors="pink blue red green black orange saddlebrown"

for i in $colors \
; do \
	magick \
		-background transparent \
		-fill $i \
		-pointsize 25 \
		-font ./devtools/NotoEmoji-Regular.ttf "label:📍" \
		-trim \
		public/images/map_pointers/pointer_$i.png \
; done

pointerslist=$(php -r "\$colors = \"$colors\"; echo implode(', ', array_map(fn (\$i) => '\"'.\$i.'\"', explode(\" \", \$colors)));")

sed -i "s/DEFAULT_POINTERS = .*$/DEFAULT_POINTERS = [$pointerslist];/g" src/Admin/Field/MapImageField.php \
