# vim: set noexpandtab :
# Copyright (c) 2009 Meding Software Technik -- All Rights Reserved.
#

.PHONY: help

# ------------------------------------------------------------------
# Help with the targets
#
help:
	@echo "The following targets are available:"
	@echo "    help       -- print this help"
	@echo "    clean      -- clean the build artifacts"
	@echo "    wurbel     -- run the wurbelizer"

# -----------------------------------------------------------------
# Wurbelizer
#
#export WURB_HOME=/usr/share/wurbelizer
WURB_PATH=$(WURB_HOME)/bin
PATH+=:$(WURB_PATH)
WURBLET_PATH=../wurblets/lib
SYSMODELPATH=`pwd`/models

wurbel:
	@echo "Wurblizing ..."
	@mkdir -p build/models
	wurbler -Dsysmodelpath=$(SYSMODELPATH) -w $(WURBLET_PATH) NetworkStatus.php
	wurbler -Dsysmodelpath=$(SYSMODELPATH) -w $(WURBLET_PATH) --line-comment \-\- network_statuses.sql


# -----------------------------------------------------------------
# Clean
#
clean:
	@rm -rf build

