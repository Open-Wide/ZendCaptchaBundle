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
     * Session key to store the code
     */
    private $key;

    /**
     * Translator
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * ZendCaptchaValidator constructor.
     *
     * @param TranslatorInterface $translator
     * @param SessionInterface $session
     * @param $key
     */
    public function __construct(TranslatorInterface $translator, SessionInterface $session, $key)
    {
        $this->translator = $translator;
        $this->session = $session;
        $this->key = $key;
    }

    /**
     * @param FormEvent $event
     */
    public function validate(FormEvent $event)
    {
        $form = $event->getForm();
        $code = $form->getData();

        $expectedCode = $this->getExpectedCode();
        if (is_null($expectedCode)) {
            $form->addError(new FormError($this->translator->trans('An error has occured')));
        } else {
            if (!$this->compare($code, $expectedCode)) {
                $form->addError(new FormError($this->translator->trans('Invalid code')));
            }
        }
    }

    /**
     * Retrieve the CAPTCHA code
     *
     * @return mixed|null
     */
    protected function getExpectedCode()
    {
        $arrayZendSession = new SessionArrayStorage();

        if ($this->session->has($this->key)) {
            $sessionKey = $this->session->get($this->key);
            $this->session->remove($this->key);

            if ($arrayZendSession->offsetExists($sessionKey)) {
                $captchaSession = $arrayZendSession->offsetGet($sessionKey);
                $arrayZendSession->offsetUnset($sessionKey);

                if ($captchaSession instanceof ArrayObject) {
                    if ($captchaSession->offsetExists('word')) {
                        $word = $captchaSession->offsetGet('word');
                        $captchaSession->offsetUnset('word');

                        return $word;
                    }
                }
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