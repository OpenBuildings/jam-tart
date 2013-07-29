tart-js:
	@echo "Updating jam-tart js..."
	@cat web/js/bootstrap-extensions/*.js > web/js/bootstrap-extensions.js
	@./vendor/twbs/bootstrap/node_modules/.bin/uglifyjs -nc web/js/bootstrap-extensions.js > web/js/bootstrap-extensions.min.tmp.js
	@echo "/*!\n* Bootstrap-Extensions.js by Ivan Kerin \n* Copyright 2013 OpenBuildings, Inc.\n* http://www.apache.org/licenses/LICENSE-2.0.txt\n*/" > web/js/copyright.js
	@cat web/js/copyright.js web/js/bootstrap-extensions.min.tmp.js > web/js/bootstrap-extensions.min.js
	@rm web/js/copyright.js web/js/bootstrap-extensions.min.tmp.js

	@cat web/js/plugins/*.js > web/js/plugins.js
	@./vendor/twbs/bootstrap/node_modules/.bin/uglifyjs -nc web/js/plugins.js > web/js/plugins.min.js

build: tart-js
	@echo "Building Bootstrap"
	@make --directory vendor/twbs/bootstrap bootstrap
	@echo "Adding jam-tart..."
	@cp ./vendor/twbs/bootstrap/bootstrap/js/bootstrap.min.js web/js/ -f
	@cp ./vendor/twbs/bootstrap/bootstrap/img/glyphicons-halflings.png web/img/ -f
	@cp ./vendor/twbs/bootstrap/bootstrap/img/glyphicons-halflings-white.png web/img/ -f
	@cp ./vendor/twbs/bootstrap/bootstrap/css/bootstrap.min.css web/css/ -f
	@cp ./vendor/twbs/bootstrap/bootstrap/css/bootstrap-responsive.min.css web/css/ -f
