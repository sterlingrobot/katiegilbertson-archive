<!DOCTYPE html>
<html lang="en" ng-app="klgApp">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <meta name="description" content="Katie Lose Gilbertson is an award winning documentary filmmaker and editor in Bozeman, specializing in Editing and Story Consulting & Development">
        <meta name="keywords" content="editor, documentary editor, film editor, video editor, bozeman, montana, pbs editor, independent lens editor">

        <title>
            {{ ( projects.currProject.name || "Freelance Video Editor" )
                + " | " + ( projects.currProject.role || "Documentary Film" ) }}
        </title>
        <link href="/css/global.css" rel="stylesheet" type="text/css" />
        <link href="/css/menu.css" rel="stylesheet" type="text/css" />
        <link href="/css/projects.css" rel="stylesheet" type="text/css" />

<?php if(isset($_GET['id'])) : ?>
        <script type="text/javascript">
            var t = setTimeout('loadData($(".project_item .image"), false)', 1000);
        </script>
<?php endif; ?>

    </head>
    <body ng-controller="ProjectController as projects">
        <div id="header">
            <?php include('menu.php'); ?>
        </div>
        <div layout="row" layout-wrap>
            <div id="content">
                <div id="backbtn" ngIf="projects.currProject">
                    <a href="/projects.php" title="Back to Projects">&nbsp;</a>
                </div>
                <div id="project_id_{{ project.id }}"
                     class="project_item fadein fast"
                     ng-repeat="project in projects.projects" flex="33">
                    <?php
                        // $url = '/p/' . GenerateUrl($project['name']) . DIRECTORY_SEPARATOR . GenerateUrl($project['role']) . DIRECTORY_SEPARATOR . $project['id'];
                        // $img_src = '/css/images/1px_spacer.gif';
                        // if(isset($_GET['id']) && file_exists(FS_ROOT . DIRECTORY_SEPARATOR . $project['images_folder'] . '/main_' . $project['id'] . '.jpg')) $img_src = resize(FS_ROOT . DIRECTORY_SEPARATOR . $project['images_folder'] . '/main_' . $project['id'] . '.jpg',$img_settings);
                        // elseif(file_exists(FS_ROOT . DIRECTORY_SEPARATOR . $project['images_folder'] . '/main.jpg')) $img_src = resize(FS_ROOT . DIRECTORY_SEPARATOR . $project['images_folder'] . '/main.jpg', $img_settings);

                    ?>

                    <a href="/p/{{ project.name | seoURL }}/{{ project.role | seoURL }}/{{ project.id }}">
                        <!-- <img class="image" ng-src="/{{ project.images_folder }}/main.jpg" /> -->
                    </a>
                    <div id="social_links">
                        <a href="//<?php echo $link; ?>" target="_blank"
                            ng-repeat="link in project.social_links.split('\n')">
                                <img src="/images/f_logo_36px.png" width="36" height="36" alt="Visit Facebook Page" />
                        </a>
                    </div>

                    <a href="/p/{{ project.name | seoURL }}/{{ project.role | seoURL }}/{{ project.id }}">
                        <h2>{{ project.name }}
                            <span class="status">( {{ project.date_completed || project.status }} )</span><br>
                            <span class="role">{{ project.role }}</span>
                        </h2>
                    </a>
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
                        <p>{{ project.description }}</p>
                    </div>
                </div>
            </div>
            <div id="footer">
                <footer>
                    <?php include('footer.php'); ?>
                </footer>
            </div>
        </div>
        <script src="/vendor/jquery/dist/jquery.min.js"></script>
        <script src="//code.jquery.com/jquery-migrate-1.0.0.js"></script>
        <script src="/vendor/jquery-cycle2/build/jquery.cycle2.min.js"></script>
        <script src="/js/threedots.js" type="text/javascript"></script>
        <script src="/js/projects.js"></script>
        <script src="vendor/angular/angular.js"></script>
        <script src="vendor/angular-animate/angular-animate.min.js"></script>
        <script src="vendor/angular-aria/angular-aria.min.js"></script>
        <script src="vendor/angular-material/angular-material.min.js"></script>
        <script src="js/app.js"></script>
        <script src="js/controllers.js"></script>
    </body>
</html>
