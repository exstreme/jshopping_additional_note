<?php
/**
 * @package plg_jshoppingcart_additional_note
 * @author exstreme <info@protectyoursite.ru>
 * @copyright Copyright © ProtectYourSite
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @version 1.0.0
 */

defined('_JEXEC') or die;

class plgJshoppingCheckoutAdditional_note extends JPlugin
{
	private $add_note_tmp=array(); // Будем хранить комментарии

    public function __construct(&$subject, $config = array())
    {
        parent::__construct($subject, $config);
    }
	
	// Добавляем ключ в корзину
	public function onBeforeSaveNewProductToCart(&$cart, &$temp_product, &$product){ 
        $temp_product['additional_note'] = '';    
    }
		
	// Выводим в корзине поле для добавления комментария
    function onBeforeDisplayCartView(&$view)
    {
		$i=0;
        foreach($view->products as $k=>$v){
            $view->products[$k]['_ext_attribute_html'].= "<div class='additional_note_div'>
			<div class='note_label'>Комментарий к товару:</div>
			<div class='note_input'>
			<input type='textarea' name='additional_note".$i."' value='".$v['additional_note']."' onchange='document.getElementById(\"note_id\").value = \"".$i."\";document.updateCart.submit();' />
			</div>
			</div>";
			$i++;
        }
    } 
	
	// Отображаем при проверке заказа введенные комментарии
	function onBeforeDisplayCheckoutCartView(&$view)
    {
        foreach($view->products as $k=>$v){
            $view->products[$k]['_ext_attribute_html'].= "<div class='additional_note_div'>
			<div class='note_label'>Комментарий к товару:</div>
			<div class='note_input'>".$v['additional_note']."</div>
			</div>";
        }
    }
	
	// Сохраняем введенные комментарии в сессии 
	function onBeforeRefreshProductInCart(&$quantity, &$cart){
		$id = JRequest::getVar('note_id',int);
		$comment = JRequest::getVar('additional_note'.$id);
		$cart->products[$id]['additional_note'] = htmlspecialchars($comment);
    }
	
	// После создания заказа помещаем комментарии в массив $add_note_tmp для отправки в шаблоне заказа
	function onAfterCreateOrder(&$order, &$cart){
		foreach($cart->products as $product){
			$this->$add_note_tmp[]=$product['additional_note'];
		}
		return $add_note_tmp;

	}

	// Передаем функции отправки данные комментариев	
	function onBeforeCreateTemplateOrderMail(&$tmp){
		for($i=0; $i < count($this->$add_note_tmp); $i++) {
			$tmp->products[$i]->additional_note=$this->$add_note_tmp[$i];
		}
	}
}