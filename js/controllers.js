'use strict';
(function() {
	angular.module('klgApp')
		.service('ProjectsService', ['$http', ProjectsService])
		.directive('cycle-slides', cycleSlides)
		.controller('ProjectController', ['ProjectsService', ProjectController]);

	function ProjectsService($http) {
		var service = {},
			projects = {};

		service.getProjects = function() {
			return $http.get('/api/projects.php').then(function(data) { return data; })
		};

		return service;
	}

	function ProjectController(ProjectsService) {
		var projects = this;

		ProjectsService.getProjects()
			.then(function(d) { projects.projects = d.data; });

		projects.setCurrProject = function(project) {
			projects.currProject = project;
		}
	}

	function cycleSlides() {

		return {
			restrict: 'AC',
			link: function (scope, element, attrs) {
				var config = angular.extend({
						slides: '.slide'
					},
					scope.$eval(attrs.cycleSlides));
				setTimeout(function () {
					element.cycle(config);
				}, 0);
			}
		};
	}
})();