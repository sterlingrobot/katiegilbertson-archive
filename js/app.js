'use strict';
(function() {
  angular.module('klgApp', ['ngMaterial'])
  	.config(ThemeProvider)
    .filter('seoURL', seoURL)
    .filter('truncate', truncate)
    .controller('AppController', ['$mdSidenav', AppController]);

  function AppController($mdSidenav){
    var app = this;

    app.menu = [
      { title: 'home', url: '/' },
      { title: 'projects', url: '/projects.php' },
      { title: 'awards', url: '/awards.php' },
      { title: 'about', url: '/about.php' },
      { title: 'contact', url: '/contact.php' }
    ];
    app.toggleSidenav = function(menuId) {
      console.log('click');
      $mdSidenav(menuId).toggle();
    };
  }

  function ThemeProvider($mdThemingProvider) {
    $mdThemingProvider.theme('default')
      .primaryPalette('green')
      .accentPalette('deep-orange');
  }

  function seoURL() {

    return function(input) {
      //Do the replacements, and convert all other non-alphanumeric characters to spaces
      input = input.replace(/(\(|\)|\[|\]|'|:)/g, '').trim().replace(/\s/g, '-');
      //Remove a - at the beginning or end and make lowercase
      input.replace(/^-/, '').replace('/-$/', '');

      return input.toLowerCase();
    }
  }

  function truncate() {

    return function(input) {

      var settings = {
          size: 180,
          omission: '...',
          ignore: true
        },
        textTruncated,
        regex    = /[!-\/:-@\[-`{-~]$/;

      if (input.length > settings.size) {
        textTruncated = input.trim()
                .substring(0, settings.size)
                .split(' ')
                .slice(0, -1)
                .join(' ');

        if (settings.ignore) {
          textTruncated = textTruncated.replace(regex, '');
        }

        return textTruncated + settings.omission;
      } else {
        return input;
      }
    }
  }


})();