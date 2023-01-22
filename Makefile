DIST=.dist
PLUGIN_NAME=$(shell basename `pwd`)
SOURCE=./*
TARGET=../target

config: clean manifest
	mkdir -p $(DIST)/$(PLUGIN_NAME)
	cp -pr _*.php *.md default-templates inc popup_posts.php config.php index.php \
	css js img locales MANIFEST COPYING tpl $(DIST)/$(PLUGIN_NAME)/
	find $(DIST) -name '*~' -exec rm \{\} \;

dist: config
	cd $(DIST); \
	mkdir -p $(TARGET); \
	zip -v -r9 $(TARGET)/plugin-$(PLUGIN_NAME)-$$(grep '/* Version' $(PLUGIN_NAME)/_define.php| cut -d"'" -f2).zip $(PLUGIN_NAME); \
	cd ..

manifest:
	@find ./ -type f|egrep -v '(*~|.git|.gitignore|.dist|target|vendor|Makefile|rsync_exclude)'|sed -e 's/\.\///' -e 's/\(.*\)/$(PLUGIN_NAME)\/&/'> ./MANIFEST

clean:
	rm -fr $(DIST)
