<?php defined('SYSPATH') or die('No direct access allowed.');

class Model_Widget_User_Registration extends Model_Widget_Decorator_Handler {

	protected $_data = array(
		'next_url' => '/',
		'reflink_next_url' => '/',
		'confirm_registration' => TRUE
	);

	public function on_page_load()
	{
		$referrer_page = Request::current()->referrer();
		$next_url = $this->get('next_url', Request::current()->referrer());

		try
		{
			// Необходимые входные данные 
			// array(
			//		'username' => '...',
			//		'password' => '...',
			//		'password_confirm' => '...',
			//		'email' => '...',
			// )
			// 
			// Создаем нового пользователя
			$user = ORM::factory('User')->create_user(Request::current()->post(), array(
				'username',
				'password',
				'email',
			));
			
			// Проверяем создался ли пользователь
			if (!$user->loaded())
			{
				throw new Kohana_Exception('User not created');
			}
			
			// Если необходимо подтверждение регистрации
			if($this->get('confirm_registration') === TRUE)
			{
				// Генерируем ссылку для подтверждения
				$reflink = ORM::factory('user_reflink')->generate($user, 'user_register', array(
					'next_url' => URL::frontend($this->get('reflink_next_url'), TRUE)
				));

				// Отправляем ссылку на email (https://github.com/butschster/kodicms/wiki/Email-Types)
				Email_Type::get('user_register')->send(array(
					'username' => $user->username,
					'email' => $user->email,
					'reflink' => Route::url('reflink', array('code' => $reflink)),
					'code' => $reflink
				));
			}
			else
			{
				// Добавляем роль login пользователю
				$role = ORM::factory('role', array('name' => 'login'));
				$user->add('roles', $role);
			}
		}
		catch (ORM_Validation_Exception $e)
		{
			Messages::errors($e->errors());
			HTTP::redirect($referrer_page);
		}
		catch (Kohana_Exception $e)
		{
			Messages::errors($e->getMessage());
			HTTP::redirect($referrer_page);
		}
		
		HTTP::redirect($next_url);
	}
}