# ------------------------------------------------------------------
# Build the wurblets
# @author <a href="mailto:uwe@uwemeding.com">Uwe B. Meding</a> 
# ------------------------------------------------------------------

WCBUILDDIR=../lib

.PHONY: build_wurblets
%.wrbl.php : %.wrbl ; wurbiler $(WCFLAGS) $< 

WSRCS=\
IncludeMappings.wrbl \
SQLCreateTable.wrbl \
PHPGetTablename.wrbl \
PHPDeclare.wrbl \
PHPMethods.wrbl \
PHPDbFieldNames.wrbl \
PHPDbGetFields.wrbl \
PHPDbSetFields.wrbl \
PHPDbInsert.wrbl \
PHPDbUpdate.wrbl \
PHPJsonSerializer.wrbl \
PHPDbSelectUnique.wrbl \
PHPDbSelectList.wrbl \
PHPDbDeleteBy.wrbl

PSRCS=\
AbstractAttributes.php\
AbstractFlexModel.php\
AbstractFlex.php\
AbstractHTMLForm.php\
AbstractModel.php\
GenericModel.php\
WSAttributes.php

WC=wurbiler
WCFLAGS=-d $(WCBUILDDIR)
WURBLETS=${WSRCS:.wrbl=.wrbl.php}

build_wurblets:	${WURBLETS}
	@echo "$(WC) done"
	@for i in $(PSRCS) ; do \
	  cp $$i $(WCBUILDDIR) ;\
	done
