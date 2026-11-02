$(document).ready(function() {
	
	/*============================================
	Page Preloader
	==============================================*/
	
	$('#page-loader span').animate({'width':'100%'},10000);
	
	$(window).load(function(){
		$('#page-loader span').stop().animate({'width':'100%'},500,function(){
			$('#page-loader').fadeOut(500);
		});
		
	});	
	
	
	/*============================================
	Header
	==============================================*/
	
	var speed = $('.header-slider').data('speed') ? parseInt($('.header-slider').data('speed'),10) : 3000;
	
	$('.header-slider').flexslider({
		animation: "fade",
		directionNav: false,
		controlNav: false,
		slideshowSpeed: speed,
		animationSpeed: 400,
		pauseOnHover:false,
		pauseOnAction:false,
		smoothHeight: false,
		slideshow:false
	});
	
	$(window).load(function(){
		if($('.header-slider').length){
			$('.header-slider').flexslider('play');
		}
	});
	
	/*============================================
	Navigation Functions
	==============================================*/
	if ($(window).scrollTop()< 10){
		$('#main-nav').removeClass('is-sticky');
	}
	else{
		$('#main-nav').addClass('is-sticky');    
	}

	$(window).scroll(function(){
		if ($(window).scrollTop()< 10){
			$('#main-nav').removeClass('is-sticky');
		}
		else{
			$('#main-nav').addClass('is-sticky');    
		}
	});
	
	/*============================================
	ScrollTo Links
	==============================================*/
	$('a.scrollto').click(function(e){
		e.preventDefault();
		var target =$(this).attr('href');
		$('html, body').stop().animate({scrollTop: $(target).offset().top}, 1600, 'easeInOutExpo',
			function(){window.location.hash =target;});
			
		if ($('.navbar-collapse').hasClass('in')){
			$('.navbar-collapse').removeClass('in').addClass('collapse');
		}
	});
	
	
	/*============================================
	Counters
	==============================================*/
	$('.counters').waypoint(function(){
		$('.counter').each(count);
	},{offset:'100%',triggerOnce:true});
	
	function count(options) {
		var $this = $(this);
		options = $.extend({}, options || {}, $this.data('countToOptions') || {});
		$this.countTo(options);
	}
	
	/*============================================
	Project thumbs - Masonry
	==============================================*/
	$(window).load(function(){

		if($('#projects-container').length){
			$('#projects-container').css({visibility:'visible'});

			$('#projects-container').masonry({
				itemSelector: '.project-item:not(.filtered)',
				isFitWidth: false,
				isResizable: true,
				isAnimated: !Modernizr.csstransitions,
				gutterWidth: 0
			});

			scrollSpyRefresh();
			waypointsRefresh();
		}
	});
	
	/*============================================
	Filter Projects
	==============================================*/
	$('#filter-works a').click(function(e){
		e.preventDefault();
		
		if($('#project-preview').hasClass('open')){
			closeProject();
		}
		
		$('#filter-works li').removeClass('active');
		$(this).parent('li').addClass('active');

		var category = $(this).attr('data-filter');

		$('.project-item').each(function(){
			if($(this).is(category)){
				$(this).removeClass('filtered');
			}
			else{
				$(this).addClass('filtered');
			}

			$('#projects-container').masonry('reload');
		});

		scrollSpyRefresh();
		waypointsRefresh();
		setTimeout(function(){
			$(window).trigger('resize');
		},500);
	});
	
	/*============================================
	Project Preview
	==============================================*/
	$('.project-item:not(.external-link)').click(function(e){
		e.preventDefault();

		var elem = $(this);
		
		if($('#project-preview').hasClass('open')){
			$('#project-preview .container').animate({'opacity':0},300);
			
			setTimeout(function(){
				$('#project-slider').flexslider('destroy');
				buildProject(elem);
			},300);
		}else{
			buildProject(elem);
		}
		
		
	});

	function buildProject(elem){
	
		var	title = elem.find('.project-title').text(),
			descr = elem.find('.project-description').html(),
			slidesHtml = '<ul class="slides">',
			elemDataCont = elem.find('.project-description');

		var hasVideo = false;
		if(elem.find('.project-description').data('video')){
			slidesHtml = slidesHtml + '<li>'+elem.find('.project-description').data('video')+'</li>';
			hasVideo = true;
		}
		
		if(elem.find('.project-description').data('images')){
			var	slides = elem.find('.project-description').data('images').split(',');
			
			for (var i = 0; i < slides.length; ++i) {
				slidesHtml = slidesHtml + '<li><img src='+slides[i]+' alt=""></li>';
			}
		}
		
		slidesHtml = slidesHtml + '</ul>';
		
		$('#project-title').text(title);
		$('#project-content').html(descr);
		$('#project-slider').html(slidesHtml);
		
		openProject(hasVideo);
	}
	
	function openProject(hasVideo){
		
		$('#project-preview').addClass('open');
		
		$('html, body').stop().animate({scrollTop: $('#filter-works').offset().top+50}, 600);
		$('#project-preview').slideDown(400);
		
		setTimeout(function(){
		
			$('#project-slider').fitVids().flexslider({
				prevText: '<i class="fa fa-angle-left"></i>',
				nextText: '<i class="fa fa-angle-right"></i>',
				animation: 'slide',
				slideshowSpeed: 3000,
				useCSS: true,
				controlNav: true, 
				pauseOnAction: false, 
				pauseOnHover: hasVideo ? false : true,
				smoothHeight: false,
				start: function(){
					if(hasVideo){$('#project-slider').find('li.clone').height(1).empty();$('#project-slider').flexslider("pause");};
					$(window).trigger('resize');
					$('#project-preview .container').animate({'opacity':1},300);
				}
			});
		},200)
	
		setTimeout(function(){
			$(window).trigger('resize');
		},800);
		
	}
	
	function closeProject(){
	
		$('#project-preview').removeClass('open');
		$('#project-preview .container').animate({'opacity':0},300);
		$('html, body').stop().animate({scrollTop: $('#filter-works').offset().top-110}, 600);
		setTimeout(function(){
			$('#project-preview').slideUp();
				
			$('#project-slider').flexslider('destroy');
			$('#project-slider').empty();
			
			scrollSpyRefresh();
			waypointsRefresh();
			
		},300);
		
		setTimeout(function(){
			$(window).trigger('resize');
		},800);
		
	}
	
	$('.close-preview').click(function(){
		closeProject();
	})
	
	/*============================================
	Google Map
	==============================================*/
	
	$(window).load(function(){
	
		if($('#gmap').length){
		
		
			var map;
			var mapstyles = [ { "stylers": [ { "saturation": -100 } ] } ];
			
			var infoWindow = new google.maps.InfoWindow;
			
			var pointLatLng = new google.maps.LatLng(mapPoint.lat, mapPoint.lng);

			var isDraggable = $('html').is('.touch') ? false : true; 
			
			var mapOptions = {
				zoom: mapPoint.zoom,
				center: pointLatLng,
				zoomControl : true,
				zoomControlOptions: {
					style: google.maps.ZoomControlStyle.LARGE,
					position: google.maps.ControlPosition.LEFT_CENTER
				},
				panControl : false,
				streetViewControl : false,
				mapTypeControl: false,
				overviewMapControl: false,
				scrollwheel: false,
				draggable:isDraggable,
				styles: mapstyles
			}
			
			map = new google.maps.Map(document.getElementById("gmap"), mapOptions);
			
			$('#gmap').waypoint(function(){
				var marker = new google.maps.Marker({
					position: pointLatLng, 
					map: map, 
					icon: mapPoint.icon
				});
				
				var mapLink = 'https://www.google.com/maps/preview?ll='+mapPoint.lat+','+mapPoint.lng+'&z=14&q='+mapPoint.mapAddress;
				
				google.maps.event.addListener(marker, 'click', function() {
					window.open(mapLink,'_blank');
				});
				
				marker.setAnimation(google.maps.Animation.DROP);
				
			},{offset:'50%',triggerOnce:true});
			
		}
		
	});		
	
	/*============================================
	Placeholder Detection
	==============================================*/
	if (!Modernizr.input.placeholder) {
		$('html').addClass('no-placeholder');
	}

	/*============================================
	Video functions
	==============================================*/
	if($('.video-container').length) $('.video-container').fitVids();

	$('.video-bg.load').each(function(){
		var $this = $(this),
			video = $this.data('video').split('.')[0];
			
		$('<source>').attr({type:'video/mp4',src:video+'.mp4'}).appendTo($this);
		$('<source>').attr({type:'video/webm',src:video+'.webm'}).appendTo($this);
		$('<source>').attr({type:'video/ogg; codecs=&quot;theora, vorbis&quot;',src:video+'.ogg'}).appendTo($this);
		
	})
		
	$(window).load(function(){
		$('.video-bg').each(function(){
			var $this = $(this),
				video = $this.data('video').split('.')[0];
				
			$('<source>').attr({type:'video/mp4',src:video+'.mp4'}).appendTo($this);
			$('<source>').attr({type:'video/webm',src:video+'.webm'}).appendTo($this);
			$('<source>').attr({type:'video/ogg; codecs=&quot;theora, vorbis&quot;',src:video+'.ogg'}).appendTo($this);
			
		})
	})
	/*============================================
	Resize Functions
	==============================================*/
	$(window).resize(function(){
	
		if($('#projects-container').length){
			$('#projects-container').masonry('reload');
		}
		
		scrollSpyRefresh();
		waypointsRefresh();
		
	});
	
	/*============================================
	Refresh scrollSpy function
	==============================================*/
	function scrollSpyRefresh(){
		setTimeout(function(){
			$('body').scrollspy('refresh');
		},1000);
		
	}

	/*============================================
	Refresh waypoints function
	==============================================*/
	function waypointsRefresh(){
		setTimeout(function(){
			$.waypoints('refresh');
		},1000);
	}
	
	/*============================================
	Contact Form
	==============================================*/
	$('#contact-form').submit(function() {
		
		if($('#contact-form').hasClass('clicked')){
			return false;
		}
		
		$('#contact-form').addClass('clicked');
		
		var buttonCopy = $('#contact-form button').html(),
			errorMessage = $('#contact-form button').data('error-message'),
			sendingMessage = $('#contact-form button').data('sending-message'),
			okMessage = $('#contact-form button').data('ok-message'),
			hasError = false;
		
		$('#contact-form .error-message').remove();
		
		$('.requiredField').each(function() {
			if($.trim($(this).val()) == '') {
				var errorText = $(this).data('error-empty');
				$(this).parents('.controls').append('<span class="error-message" style="display:none;">'+errorText+'.</span>').find('.error-message').fadeIn('fast');
				$(this).addClass('inputError');
				hasError = true;
			} else if($(this).is("input[type='email']") || $(this).attr('name')==='email') {
				var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
				if(!emailReg.test($.trim($(this).val()))) {
					var invalidEmail = $(this).data('error-invalid');
					$(this).parents('.controls').append('<span class="error-message" style="display:none;">'+invalidEmail+'.</span>').find('.error-message').fadeIn('fast');
					$(this).addClass('inputError');
					hasError = true;
				}
			}
		});
		
		if(hasError) {
			$('#contact-form button').html('<i class="fa fa-times"></i>'+errorMessage);
			setTimeout(function(){
				$('#contact-form button').html(buttonCopy);
				$('#contact-form').removeClass('clicked');
			},2000);
		}
		else {
			$('#contact-form button').html('<i class="fa fa-spinner fa-spin"></i>'+sendingMessage);
			
			var formInput = $(this).serialize();
			$.post($(this).attr('action'),formInput, function(data){
				$('#contact-form button').html('<i class="fa fa-check"></i>'+okMessage);
				setTimeout(function(){
					$('#contact-form button').html(buttonCopy);
				$('#contact-form').removeClass('clicked');
				},2000);
				
				$('#contact-form')[0].reset();
			});
		}
		
		return false;	
	});
	

/*============================================
	Contact Form 2
	==============================================*/
	$('#contact-form2').submit(function() {
		
		if($('#contact-form2').hasClass('clicked')){
			return false;
		}
		
		$('#contact-form2').addClass('clicked');
		
		var buttonCopy = $('#contact-form2 button').html(),
			errorMessage = $('#contact-form2 button').data('error-message'),
			sendingMessage = $('#contact-form2 button').data('sending-message'),
			okMessage = $('#contact-form2 button').data('ok-message'),
			hasError = false;
		
		$('#contact-form2 .error-message').remove();
		
		$('.requiredField2').each(function() {
			if($.trim($(this).val()) == '') {
				var errorText = $(this).data('error-empty');
				$(this).parents('.controls').append('<span class="error-message" style="display:none;">'+errorText+'.</span>').find('.error-message').fadeIn('fast');
				$(this).addClass('inputError');
				hasError = true;
			} else if($(this).is("input[type='email']") || $(this).attr('name')==='email') {
				var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
				if(!emailReg.test($.trim($(this).val()))) {
					var invalidEmail = $(this).data('error-invalid');
					$(this).parents('.controls').append('<span class="error-message" style="display:none;">'+invalidEmail+'.</span>').find('.error-message').fadeIn('fast');
					$(this).addClass('inputError');
					hasError = true;
				}
			}
		});
		
		if(hasError) {
			$('#contact-form2 button').html('<i class="fa fa-times"></i>'+errorMessage);
			setTimeout(function(){
				$('#contact-form2 button').html(buttonCopy);
				$('#contact-form2').removeClass('clicked');
			},2000);
		}
		else {
			$('#contact-form2 button').html('<i class="fa fa-spinner fa-spin"></i>'+sendingMessage);
			
			var formInput = $(this).serialize();
			$.post($(this).attr('action'),formInput, function(data){
				$('#contact-form2 button').html('<i class="fa fa-check"></i>'+okMessage);
				setTimeout(function(){
					$('#contact-form2 button').html(buttonCopy);
				$('#contact-form2').removeClass('clicked');
				},2000);
				
				$('#contact-form2')[0].reset();
			});
		}
		
		return false;	
	});





	/*============================================
	Newsletter
	==============================================*/
	$(window).load(function(){
		$('.autopopup').each(function(){
			var elem = $(this);
			var delay = elem.data('delay');
			setTimeout(function(){
				elem.modal('show');
			},delay)
		})
	})
	
	$('.mc-newsletter').each(function(){
		var listUrl = $(this).data('list-url');
		$(this).ajaxChimp({
			callback: callbackMailchimp,
			url: listUrl
		});
		
	});
		
	function callbackMailchimp (resp) {
		
		var currform = $('.mc-newsletter.mc-current');
		
		currform.find('.mc-validation p').hide();
		currform.removeClass('sending');
		currform.removeClass('mc-current');
		
		if(resp.result === 'success'){
	
			currform.find('.mc-text-if-ok').show();
			currform[0].reset();
			
		}else{
		
			if(resp.msg.split(' - ',2)[0] === '0'){
				currform.find('.mc-text-if-invalid').show();
			}else{
				currform.find('.mc-text-if-exist').show();
			}
		
		}
		
	}
	
	$('.mc-newsletter button').click(function(e) {

		e.preventDefault();

		var $form = $(this).parents('.mc-newsletter');
		
		$('.mc-newsletter').removeClass('mc-current');
		$form.addClass('mc-current');
		
		if($form.is('.sending')){
			return false;
		}
	
		$form.find('.mc-validation p').hide();
		$form.removeClass('sending');
		
		var hasError 	= false,
			$emailInput = $form.find('[type=email]'),
			emailReg 	= /^([\w-\.]+@([\w-]+\.)+[\w-]{2,8})?$/;
			
		
		if($.trim($emailInput.val()) == '') {
			$form.find('.mc-text-if-empty').show();
			hasError = true;
		} else if(!emailReg.test($.trim($emailInput.val()))) {
			$form.find('.mc-text-if-invalid').show();
			hasError = true;
		}
		
		if(!hasError) {
		
			$form.find('.mc-text-if-sending').show();
			$form.addClass('sending');
			
			$form.submit();
		}
		
		return false;	
	});
	
});	