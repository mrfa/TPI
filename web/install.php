<?php

/**
 * Script d'installation modifié pour configurer l'intranet
 * 
 * 
 * @author Fabrice Maillefer
 */

if (!isset($_SERVER['HTTP_HOST'])) {
	exit('This script cannot be run from the CLI. Run it from a browser.');
}

if (!in_array(@$_SERVER['REMOTE_ADDR'], array('127.0.0.1', '::1',))) {
	header('HTTP/1.0 403 Forbidden');
	exit('This script is only accessible from localhost.');
}

require_once dirname(__FILE__) . '/../app/SymfonyRequirements.php';

$symfonyRequirements = new SymfonyRequirements();

$majorProblems = $symfonyRequirements->getFailedRequirements();
$minorProblems = $symfonyRequirements->getFailedRecommendations();

?>
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
        <meta name="robots" content="noindex,nofollow" />
        <title>Intranet PME - Configuration</title>
        <link rel="stylesheet" href="bundles/framework/css/structure.css" media="all" />
        <link rel="stylesheet" href="bundles/framework/css/body.css" media="all" />
        <link rel="stylesheet" href="bundles/sensiodistribution/webconfigurator/css/install.css" media="all" />
    </head>
    <body>
        <div id="content">
            <div class="header clear-fix">
                <div class="header-logo">
                    <img style="vertical-align:middle;" src="/intranet.png" alt="intranet" />
                  	<h1 style="display:inline; font-size:3em;">Intranet Gestion</h1>  
                </div>
            </div>

            <div class="sf-reset">
                <div class="block">
                    <div class="symfony-block-content">
                        <h1 class="title">Bienvenue !</h1>
                        <p>Bienvenue dans l'installation de l'intranet de gestion de comptabilité pour une PME.</p>
 						<p>
 							Ce script va permettre d'installer et de configurer le projet.
                        </p>
						<p>
 							Avant de continuer, veuillez vérifier votre configuration de votre serveur WEB selon les recommendations ci-après :
                        </p>
                        <?php if (count($majorProblems)): ?>
                            <h2 class="ko">Major problems</h2>
                            <p>Problèmes majeur qui <strong>doivent</strong> être corrigé avant de continuer:</p>
                            <ol>
                                <?php foreach ($majorProblems as $problem): ?>
                                    <li><?php echo $problem->getHelpHtml() ?></li>
                                <?php endforeach; ?>
                            </ol>
                        <?php endif; ?>

                        <?php if (count($minorProblems)): ?>
                            <h2>Recommendations</h2>
                            <p>
                                <?php if (count($majorProblems)): ?>En plus, pour<?php else: ?>Pour<?php endif; ?> améliorer les performances de l'application,
                                il est recommendé de corriger les problèmes ci-dessous:
                            </p>
                            <ol>
                                <?php foreach ($minorProblems as $problem): ?>
                                    <li><?php echo $problem->getHelpHtml() ?></li>
                                <?php endforeach; ?>
                            </ol>
                        <?php endif; ?>

                        <?php if ($symfonyRequirements->hasPhpIniConfigIssue()): ?>
                            <p id="phpini">*
                                <?php if ($symfonyRequirements->getPhpIniConfigPath()): ?>
                                    Les changements du fichier <strong>php.ini</strong> devront être fait dans "<strong><?php echo $symfonyRequirements->getPhpIniConfigPath() ?></strong>".
                                <?php else: ?>
                                    Pour changer les paramêtres, créez un fichier "<strong>php.ini</strong>".
                                <?php endif; ?>
                            </p>
                        <?php endif; ?>

                        <?php if (!count($majorProblems) && !count($minorProblems)): ?>
                            <p class="ok">Votre configuration semble correct pour executer l'application.</p>
                        <?php endif; ?>

                        <ul class="symfony-install-continue">
                            <?php if (!count($majorProblems)): ?>
                                <li><a href="app_dev.php/_configurator/">Configurer l'application en ligne</a></li>
                            <?php endif; ?>
                            <?php if (count($majorProblems) || count($minorProblems)): ?>
                                <li><a href="install.php">Re-contrôler la configuration</a></li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="version">Symfony Maillefer Edition</div>
        </div>
    </body>
</html>
