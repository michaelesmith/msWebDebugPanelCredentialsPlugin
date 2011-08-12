<?php

/**
 * msWebDebugPanelCredentials
 *
 * Adds a panel to the web debug bar that lets the user add or remove credentials to the current user on the fly
 *
 * @author msmith
 */
class msWebDebugPanelCredentials extends sfWebDebugPanel {

	/**
	 * Listens to the debug.web.load_panels event.
	 * 
	 * @param sfEvent $event 
	 * @static
	 */
	static public function listenToAddPanelEvent(sfEvent $event) {
		$event->getSubject()->setPanel('assets', new msWebDebugPanelCredentials($event->getSubject()));
	}

	/**
	 * Listens to the routing.load_configuration event.
	 *
	 * @param sfEvent An sfEvent instance
	 * @static
	 */
	static public function listenToRoutingLoadConfigurationEvent(sfEvent $event) {
		$r = $event->getSubject();

		// preprend our routes
		$r->prependRoute('msWebDebugPanelCredentials', new sfRoute('/msWebDebugPanelCredentials/:type/:subject', array('module' => 'msWebDebugPanelCredentials', 'action' => 'index')));
	}

	public function getTitle() {
		return '<img src="/msWebDebugPanelCredentialsPlugin/images/lock_open.png" />credentials';
	}

	public function getPanelTitle() {
		return 'Add or Remove sfDoctrineGuard Credentials';
	}

	public function getPanelContent() {
		$credentials = sfGuardPermissionTable::getInstance()->createQuery('p')->orderBy('p.name')->execute();
		$groups = sfGuardGroupTable::getInstance()->createQuery('g')->orderBy('g.name')->execute();
		$routing = sfContext::getInstance()->getRouting();
		$user = sfContext::getInstance()->getUser();

		if(!$user->isAuthenticated()){
			return 'The current user is not authenticated';
		}

		$user_html = sprintf('<h2>Currently logged in as %s - ', $user->getGuardUser()->getUsername());
		$user_html .= $user->getGuardUser()->getIsSuperAdmin() ? 'Notice: This user has super admin and will have all credentials no matter what is choosen here</h2>' : 'Super admin is off</h2>';

		$delete = sprintf('<h2>Current Credentials</h2>Clicking will remove<ul><li><a href="%s"><img src="/msWebDebugPanelCredentialsPlugin/images/cross.png" />Remove All</a></li>',
							 $routing->generate('msWebDebugPanelCredentials', array('type' => 'credential', 'subject' => 'all'))
		);

		$add = '<h2>Other Credentials</h2>Clicking will add<ul>';

		foreach ($credentials as $credential) {
			$html = sprintf('<li><a href="%s"><img src="/msWebDebugPanelCredentialsPlugin/images/%s.png" />%s</a></li>',
								 $routing->generate('msWebDebugPanelCredentials', array('type' =>'credential', 'subject' => str_replace('.', '*', $credential->getName()))),
								 $user->hasCredential($credential->getName()) ? 'delete' : 'add',
								 $credential->getName()
			);
			if ($user->hasCredential($credential->getName())) {
				$delete .= $html;
			} else {
				$add .= $html;
			}
		}

		$delete .= '</ul>';
		$add .= '</ul>';

		$group_html = '<h2>Groups</h2>Clicking will add<ul>';
		foreach ($groups as $group) {
			$group_html .= sprintf('<li><a href="%s"><img src="/msWebDebugPanelCredentialsPlugin/images/add.png" />%s</a></li>',
								 $routing->generate('msWebDebugPanelCredentials', array('type' =>'group', 'subject' => str_replace('.', '*', $group->getName()))),
								 $group->getName()
			);
		}

		$group_html .= '</ul>';

		return "<table><tr><td>$delete</td><td>$add</td><td>$group_html</td></tr></table>$user_html";
	}

}
