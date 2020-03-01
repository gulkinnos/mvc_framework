<?php


class ContactsController extends Controller
{

	public function __construct($controller, $action) {
		parent::__construct($controller, $action);
		$this->view->setLayout('default');
		$this->load_model('Contacts');
	}

	public function indexAction() {

		/**
		 * Declaring datatype to help IDE figure out with suggestions.
		 *
		 * @var Contacts $contactsModel
		 */
		$contactsModel = new $this->ContactsModel;
		$contacts      = $contactsModel->findAllByUserId(Helpers::currentUser()->id, ['order' => 'lname, fname']);

		$this->view->contacts = $contacts;
		$this->view->render('contacts/index');
	}

	public function addAction() {
		$contact    = new Contacts;
		$validation = new Validate();

		if($_POST) {
			$contact->assign($_POST);
			$validation->check($_POST, Contacts::$addValidation);
			if($validation->passed()) {
				$contact->user_id = Helpers::currentUser()->id;
				$contact->save();
				Router::redirect('contacts');
			}
		}
		$this->view->contact       = $contact;
		$this->view->displayErrors = $validation->displayErrors();
		$this->view->postAction    = PROOT . 'contacts' . DS . 'add';
		$this->view->render('contacts/add');
	}

	public function detailsAction($id) {
		$contact = $this->ContactsModel->findByIdAndUserId((int)$id, Helpers::currentUser()->id);

		if(!$contact) {
			Router::redirect('contacts');
		}
		$this->view->contact = $contact;
		$this->view->render('contacts/details');
	}

	public function deleteAction($id) {
		$contact = $this->ContactsModel->findByIdAndUserId((int)$id, Helpers::currentUser()->id);
		if($contact) {
			$contact->delete();
		}
		Router::redirect('contacts');
	}

}