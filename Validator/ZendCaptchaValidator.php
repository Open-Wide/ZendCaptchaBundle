<?php

namespace Openwide\Bundle\ZendCaptchaBundle\Validator;

use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Translation\TranslatorInterface;

use Zend\Session\Storage\SessionArrayStorage;
use Zend\Stdlib\ArrayObject;

/**
 * ZendCaptcha validator
 */
class ZendCaptchaValidator
{
    /**
     * @var SessionInterface
     */
    private $session;

    /**
     * Translator
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * Configuration parameter used to bypass a required code match
     */
    private $bypassCode;

    /**
     * ZendCaptchaValidator constructor.
     *
     * @param TranslatorInterface $translator
     * @param SessionInterface $session
     */
    public function __construct(TranslatorInterface $translator, SessionInterface $session, $bypassCode)
    {
        $this->translator = $translator;
        $this->session = $session;
        $this->bypassCode = $bypassCode;
    }

    /**
     * @param FormEvent $event
     */
    public function validate(FormEvent $event)
    {
        $form = $event->getForm();
        $sessionKey = sprintf('%s_zend_captcha_session_key_%s', $form->getParent()->getName(), $form->getName());

        $code = $form->getData();

        $expectedCode = $this->getExpectedCode($sessionKey);

        if (is_null($expectedCode)) {
            $form->addError(new FormError($this->translator->trans('An error has occured')));
        } else {
            if ($this->compare($code, $expectedCode) == false && $this->compare($code, $this->bypassCode) == false ) {
                $form->addError(new FormError($this->translator->trans('Invalid code')));
            }
        }
    }

    /**
     * Retrieve the CAPTCHA code
     *
     *@param $key
     *
     * @return mixed|null
     */
    protected function getExpectedCode($key)
    {
        $arrayZendSession = new SessionArrayStorage();

        if ($this->session->has($key)) {
            $sessionKey = $this->session->get($key);
            $this->session->remove($key);

            $captchaSession = $arrayZendSession->offsetGet($sessionKey);
            $arrayZendSession->offsetUnset($sessionKey);
            if ($captchaSession instanceof ArrayObject) {
                $word = $captchaSession->offsetGet('word');
                $captchaSession->offsetUnset('word');

                return $word;
            }
        }
        return null;
    }

    /**
     * Run a match comparison on the provided code and the expected code
     *
     * @param $code
     * @param $expectedCode
     *
     * @return bool
     */
    protected function compare($code, $expectedCode)
    {
        return ($expectedCode !== null && is_string($expectedCode) && ($code !== null && is_string($code)) && ($code == $expectedCode));
    }

}