/*************************************************************
 *  ShareMaster
 *  Author: 
 *  Author URI: 
 *  Description:.
 *  Version:
 *  License: SM proj.
**************************************************************/

window["smobj"] = {
    getExt : function(filename) {
        return filename.split('.').pop();
    }
};

Dropzone.options.shareMasterUpload = {
    maxFilesize: 0.5, // MB
    accept: function(file, done) {
        console.log("validating", smobj.getExt(file.name));
        if (smobj.getExt(file.name) === "png") {
            done("Naha, you don't.");
        } else { done(); }
    },
    uploadMultiple: false,
    parallelUploads: 5,
    addRemoveLinks: true,
    dictRemoveFile: 'Remove',
    dictFileTooBig: 'Image is bigger than 500KB',
    
    // The setting up of the dropzone
    init:function() {

        this.on("removedfile", function(file) {

            $.ajax({
                type: 'POST',
                url: 'core/filerm.php',
                data: { id: file.name },
                dataType: 'html',
                success: function(data){
                    var rep = JSON.parse(data);
                    console.log(rep);
                }
            });

        });
    },
    error: function(file, response) {
        if($.type(response) === "string")
            var message = response; //dropzone sends it's own error messages in string
        else
            var message = response.message;
        file.previewElement.classList.add("dz-error");
        _ref = file.previewElement.querySelectorAll("[data-dz-errormessage]");
        _results = [];
        for (_i = 0, _len = _ref.length; _i < _len; _i++) {
            node = _ref[_i];
            _results.push(node.textContent = message);
        }
        return _results;
    },
    success: function(file,done) {
        console.log(done);
        var rep = JSON.parse(done);
        console.log(rep);
    }
};

$(function() {
    
/*** Nav Bar actions: ***/
(function($, window, document){
    var slide_speed = "fast";
    var selector_trigger = 'li.add-collapse';
    var selector_state = 'menu-collapsed';
    var selector_menu = 'ul.add-menu';
    var selector_hamburger = '.hamburger-nav';
  	//Collapse:
  	$(selector_trigger).click(function() {
		var $this = $(this);
      	var $menu = $this.find(selector_menu).eq(0);
        if ($menu.length) {
         	if ($this.hasClass(selector_state)) {
              $menu.slideUp(slide_speed,function(){
            	$this.removeClass(selector_state);
              });
            } else {
              //Close others:
              $(document).trigger("click");
              $this.addClass(selector_state);
              $menu.slideDown(slide_speed,function(){
              });
            }
        }
    });
    //Auto hide nav collapse:
    $(document).click(function(e) {
      if ($(e.target).is(
        	selector_trigger + ", " + selector_trigger + " *")) {
            return;
        }
		$(selector_trigger + "." + selector_state).each(function(i,el) {
            $(el).find(selector_menu).eq(0).slideUp(slide_speed,function(){
            	$(el).removeClass(selector_state);
            });
        });
    });
    //hamburger trigger:
    $(selector_hamburger).click(function(){
      	var $this = $(this);
      	var $con = $this.next("div");
      	$con.toggleClass("hide-nav-xs");
    });
  
}(jQuery, window, document));

  /*** Page Tabs: ***/
(function($, window, document){
  
    $('#page-tabs a').click(function (e) {
      e.preventDefault()
      $(this).tab('show')
    });
  
}(jQuery, window, document));
  
  
});