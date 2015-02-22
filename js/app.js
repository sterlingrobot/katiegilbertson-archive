'use strict';
(function() {
  angular.module('klgApp', ['ngMaterial', 'ngAnimate', 'ngAria'])
  	.config(ThemeProvider)
  	.filter('seoURL', seoURL);

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
})();