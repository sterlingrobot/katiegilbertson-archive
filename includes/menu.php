<div id="nav">
    <nav>
        <ul>
            <li>&nbsp;</li>
            <li class="<?php if(strpos($_SERVER['PHP_SELF'], 'index') > -1) echo 'selected'; ?>"><a href="/index.php" >Home</a></li>
            <!--<li class="<?php if(strpos($_SERVER['PHP_SELF'], 'reel') > -1) echo 'selected'; ?>"><a href="reel.php" >Reel</a></li>
            <li class="<?php if(strpos($_SERVER['PHP_SELF'], 'resume') > -1) echo 'selected'; ?>"><a href="resume.php" >Resume</a></li>-->
            <li class="<?php if(strpos($_SERVER['PHP_SELF'], 'awards') > -1) echo 'selected'; ?>"><a href="/awards.php" >Awards</a></li>
            <li class="<?php if(strpos($_SERVER['REQUEST_URI'], '/p/') > -1 || strpos($_SERVER['PHP_SELF'], 'projects') > -1) echo 'selected'; ?> projects"><a href="/projects.php"  >Projects</a></li>
<!--                <ul class="subprojects">
                    <li class="<?php //if(strpos($_SERVER['PHP_SELF'], 'current') > -1) echo 'selected'; ?> "><a href="/projects.php?status=current" >Current</a></li>
                    <li class="<?php //if(strpos($_SERVER['PHP_SELF'], 'completed') > -1) echo 'selected'; ?> "><a href="/projects.php?status=completed" >Completed</a></li>
                    </ul>
            <br class="clearfloat"/>-->
            <li class="<?php if(strpos($_SERVER['PHP_SELF'], 'about') > -1) echo 'selected'; ?>"><a href="/about.php" >About Me</a></li>
            <li class="<?php if(strpos($_SERVER['PHP_SELF'], 'contact') > -1) echo 'selected'; ?>"><a href="/contact.php" >Contact</a></li>
            <li>&nbsp;</li>
            <li>&nbsp;</li>
            <li>&nbsp;</li>
        </ul>
    </nav>
</div>
