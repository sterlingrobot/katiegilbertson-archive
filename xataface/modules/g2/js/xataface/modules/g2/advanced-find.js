//require <jquery.packed.js>
//require <xatajax.core.js>
//require <xatajax.util.js>
(function(){
	var $ = jQuery;
	
	$(document).ajaxError(function(e, xhr, settings, exception) {
	   if ( !console ) return;
	   console.log(e);
	   console.log(xhr);
	   console.log(settings);
	   console.log(exception);
	});
	
	
	var g2 = XataJax.load('xataface.modules.g2');
	g2.AdvancedFind = AdvancedFind;
	
	function AdvancedFind(/**Object*/ o){
		this.table = $('meta#xf-meta-tablename').attr('content');
		this.el = $('<div>').addClass('xf-advanced-find').css('display','none').get(0);

		$.extend(this, o);
		this.loaded = false;
		this.loading = false;
		this.installed = false;
	}
	
	$.extend(AdvancedFind.prototype, {
	
		load: load,
		ready: ready,
		show: show,
		hide: hide,
		install: install
	});
	
	
	function load(/**Function*/ callback){
		callback = callback || function(){};
		var self = this;
		$(this.el).load(DATAFACE_SITE_HREF+'?-table='+encodeURIComponent(this.table)+'&-action=g2_advanced_find_form', function(){
			
			var params = XataJax.util.getRequestParams();
			var widgets = [];
			var formEl = this;
			
			$('[name]', this).each(function(){
				if ( params[$(this).attr('name')] ){
					$(this).val(params[$(this).attr('name')]);
				}
				var widget = null;
				
				if ( $(this).attr('data-xf-find-widget-type') ){
					widget = $(this).attr('data-xf-find-widget-type');
				} else if ( $(this).get(0).tagName.toLowerCase() == 'select' ){
					widget = 'select';
				} 
				if ( widget ){
					widgets.push('xataface/findwidgets/'+widget+'.js');
				}
				
			});
			
			
			
			if ( widgets.length > 0 ){
				XataJax.util.loadScript(widgets.join(','), function(){
					self.loaded = true;

					callback.call(self);
					
					$('[name]', formEl).each(function(){
						if ( params[$(this).attr('name')] ){
							$(this).val(params[$(this).attr('name')]);
						}
						var widget = null;
						
						if ( $(this).attr('data-xf-find-widget-type') ){
							widget = $(this).attr('data-xf-find-widget-type');
						} else if ( $(this).get(0).tagName.toLowerCase() == 'select' ){
							widget = 'select';
						} 
						if ( widget ){
							var w = new xataface.findwidgets[widget]();
							w.install(this);
							
						}
						
					});
					
					
					$('button.xf-advanced-find-clear', formEl).click(function(){
						$('input[name],select[name]', formEl).val('');
						return false;
					});
					
					$('button.xf-advanced-find-search', formEl).click(function(){
						$(this)
							
							.parents('form').submit();
					});
					
					$(self).trigger('onready');
						
				});
			} else {
				
				self.loaded = true;
				callback.call(self);
				$(self).trigger('onready');
			}
		});
	}
	
	
	function ready(/**Function*/ callback){
		if ( this.loaded ){
			callback.call(this);
		} else {
			$(this).bind('onready', callback);
			if ( !this.loading ){
				this.load();
			}
		}
		
	}
	
	function install(){
		if ( this.installed ) return;
		$(this.el).insertAfter('a.xf-show-advanced-find');
		this.installed = true;
		
	}
	
	function show(){
		//alert('hello');
		this.ready(function(){
			//alert('now');
			if ( !this.loaded ) throw "Cannot show advanced find until it is ready.";
			//alert('here');
			if ( !this.installed ) this.install();
			//alert('here');
			if ( !$(this.el).is(':visible') ){
				//alert(this.el);
				$(this.el).slideDown(function(){
					// Make sure it is only the width of the window.
					var x = $(this).offset().left;
					//alert(x);
					$(this).width($(window).width()-x-5);
				});
			}
		});
	}
	
	function hide(){
		this.ready(function(){
			if ( !this.loaded || !this.installed ) return;
			if ( $(this.el).is(':visible') ){
				$(this.el).slideUp();
			}
		});
	}
	
	

})();