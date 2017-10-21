'use strict';
(function() {

	angular.module('klgApp')
		.service('ProjectsService', ['$http', ProjectsService]);

	function ProjectsService($http) {
		var service = {},
			projects = {};

		service.getProjects = function() {
			return $http.get('/api/projects.php').then(function(data) { return data; })
		};

		return service;
	}

})();