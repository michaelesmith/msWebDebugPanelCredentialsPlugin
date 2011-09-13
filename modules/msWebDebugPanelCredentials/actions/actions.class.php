<?php

/**
 * msWebDebugPanelCredentials actions.
 *
 * @package    synoffice
 * @subpackage msWebDebugPanelCredentials
 * @author     msmith
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class msWebDebugPanelCredentialsActions extends sfActions {

	/**
	 * Executes index action
	 *
	 * @param sfRequest $request A request object
	 */
	public function executeIndex(sfWebRequest $request) {
		if($request->getParameter('type') == 'group'){
			$this->getUser()->clearCredentials();
			foreach(sfGuardGroupTable::getInstance()->findOneBy('name', str_replace('*', '.', $request->getParameter('subject')))->getPermissions() as $permission){
				$this->getUser()->addCredential($permission->getName());
			}
		}else{
			$credential = str_replace('*', '.', $request->getParameter('subject'));
			if($credential == 'all'){
				$this->getUser()->clearCredentials();
			}elseif($this->getUser()->hasCredential($credential)){
				$this->getUser()->removeCredential($credential);
			}else{
				$this->getUser()->addCredential($credential);
			}
		}

		$this->redirect($request->getReferer());
	}

	public function executeMasquerade(sfWebRequest $request) {
		if($request->hasParameter('user_id')){
			$user = sfGuardUserTable::getInstance()->find($request->getParameter('user_id'));
			$this->forward404Unless($user);

			if($this->getUser()->hasAttribute('msWebDebugPanelCredentials.masquerade')){
				$masquerade = $this->getUser()->getAttribute('msWebDebugPanelCredentials.masquerade');
			}else{
				$masquerade[$this->getUser()->getGuardUser()->id] = sprintf('%s (%s)', $this->getUser()->getGuardUser()->username, $this->getUser()->getGuardUser()->__toString());
			}
			$masquerade[$user->id] = sprintf('%s (%s)', $user->username, $user->__toString());
			$this->getUser()->setAttribute('msWebDebugPanelCredentials.masquerade', $masquerade);

			$this->getUser()->signOut();
			$this->getUser()->signIn($user);
		}

		$this->users = sfGuardUserTable::getInstance()->createQuery('u')->orderBy('u.username')->execute();
	}

}
