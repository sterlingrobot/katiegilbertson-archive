<?php require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/configure.php'); ?>

<!DOCTYPE html>
<html lang="en" ng-app="klgApp" ng-controller="AppController as app">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <meta name="description" content="Katie Lose Gilbertson is an award winning documentary filmmaker and editor in Bozeman, specializing in Editing and Story Consulting & Development">
        <meta name="keywords" content="editor, documentary editor, film editor, video editor, bozeman, montana, pbs editor, independent lens editor">

        <title>
            {{ ( projects.currProject.name || "Freelance Video Editor" )
                + " | " + ( projects.currProject.role || "Documentary Film" ) }}
        </title>
        <link href="/css/global.css" rel="stylesheet" type="text/css" />
        <!-- <link href="/css/menu.css" rel="stylesheet" type="text/css" /> -->
        <!-- <link href="/css/projects.css" rel="stylesheet" type="text/css" /> -->

        <link href="/vendor/angular-material/angular-material.css" rel="stylesheet">
        <style>
            md-grid-tile { transition: all 400ms ease-out 50ms;}
            .award { display: none;}
        </style>
<?php if(isset($_GET['id'])) : ?>
        <script type="text/javascript">
            var t = setTimeout('loadData($(".project_item .image"), false)', 1000);
        </script>
<?php endif; ?>

    </head>
    <body>
        <div ng-include="'/includes/menu.html'"> </div>
        <div ng-controller="ProjectController as projects">

            <md-grid-list
                md-cols-sm="1" md-cols-md="2" md-cols-gt-md="4"
                md-row-height-gt-md="4:3" md-row-height="4:3"
                md-gutter="8px" md-gutter-gt-sm="4px"
                md-on-layout="console.log('layout')">

                <md-grid-tile ng-repeat="project in projects.projects" ng-click="projects.setCurrProject(project);" md-rowspan="2">
                    <md-card>
                        <a href="/p/{{ project.name | seoURL }}/{{ project.role | seoURL }}/{{ project.id }}">
                            <img class="image" ng-src="/{{ project.images_folder }}/main.jpg" width="120" />
                        </a>
                        <md-card-content>
                            <h3>{{ project.name }}
                                <span class="status">( {{ project.date_completed || project.status }} )</span><br>
                                <span class="role">{{ project.role }}</span>
                            </h3>

                            <span class="employer">{{ project.employer ? 'For ' + project.employer : '' }}</span>

                            <div class="awards synopsis" cycle-slides>
                                <div class="award laurel slide" ng-repeat="award in project.awards"
                                    ng-class="{ 'hasimage' : award.laurel_image }">
                                        <img ng-src="{{ award.laurel_image || '/css/images/1px_spacer.gif' }}"
                                            height="{{ award.laurel.image ? '80' : '1' }}" />
                                    <h4 class="provider">{{ award.provider }}</h4>
                                    <p class="awardname">{{ award.award }}</p>
                                </div>
                            </div>

                            <div id="project_id_{{project.id}}_data" class="data" style="display: none; margin-top: 1em;"> </div>

                            <div class="subproject_item project_item fadein fast" id="project_id_{{subproject.id}}" ng-repeat="subproject in project.subprojects">
                                <a class="subproject"
                                    href="/p/{{ subproject.name  }}/{{ subproject.role }}/{{ subproject.id }}" alt="{{ subproject.name + ' (' + subproject.date_completed }} )" title="{{ subproject.name + ' (' + subproject.date_completed }} )">
                                    <img ng-src="/{{ subproject.images_folder }}/main_{{ subproject.id }}.jpg" border="0" />
                                </a>
                            </div>

                            <div class="project_desc">
                                <p>{{ project.description | truncate }}</p>
                            </div>
                            <div class="social_links">
                                <a href="//{{ link }}" target="_blank"
                                    ng-repeat="link in project.social_links.split('\n')">
                                        <img src="/images/f_logo_36px.png" width="36" height="36" alt="Visit Facebook Page" />
                                </a>
                            </div>
                         </md-card-content>
                    </md-card>
                </md-grid-tile>
            </md-grid-list>
        </div>
        <div id="footer">
            <footer>
                <?php include('footer.php'); ?>
            </footer>
        </div>
<!--        <script src="/vendor/jquery/dist/jquery.min.js"></script>
        <script src="//code.jquery.com/jquery-migrate-1.0.0.js"></script>
        <script src="/vendor/jquery-cycle2/build/jquery.cycle2.min.js"></script>
        <script src="/js/projects.js"></script> -->
        <script src="vendor/angular/angular.js"></script>
        <script src="vendor/angular-material/angular-material.min.js"></script>
        <script src="vendor/angular-animate/angular-animate.min.js"></script>
        <script src="vendor/angular-aria/angular-aria.min.js"></script>
        <script src="js/app.js"></script>
        <script src="js/controllers.js"></script>
    </body>
</html>
