<?php

namespace Openwide\Bundle\ZendCaptchaBundle\Form\Type;

use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Translation\TranslatorInterface;

use Zend\Captcha\Image;

use Openwide\Bundle\ZendCaptchaBundle\Validator\ZendCaptchaValidator;

class ZendCaptchaType extends AbstractType
{
    /**
     * @var SessionInterface
     */
    protected $session;

    /**
     * Options
     * @var array
     */
    private $options = array();

    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * ZendCaptchaType constructor.
     *
     * @param array $options
     */
    public function __construct(Filesystem $filesystem, SessionInterface $session, TranslatorInterface $translator, array $options)
    {
        try {
            $filesystem->mkdir($options['img_dir']);
        } catch (IOExceptionInterface $e) {
            echo "An error occurred while creating your directory at ".$e->getPath();
        }
        $this->translator = $translator;
        $this->options = $options;
        $this->session = $session;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $validator = new ZendCaptchaValidator(
            $this->translator,
            $this->session
        );

        $builder->addEventListener(FormEvents::POST_SUBMIT, array($validator, 'validate'));
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $sessionKey = sprintf('%s_zend_captcha_session_key_%s', $form->getParent()->getName(), $form->getName());

        $captcha = new Image();

        if (isset($options['suffix'])) {
            $captcha->setSuffix($options['suffix']);
        }
        if (isset($options['height'])) {
            $captcha->setHeight($options['height']);
        }
        if (isset($options['width'])) {
            $captcha->setWidth($options['width']);
        }
        if (isset($options['img_alt'])) {
            $captcha->setImgAlt($options['img_alt']);
        }
        if (isset($options['font_size'])) {
            $captcha->setFontSize($options['font_size']);
        }
        if (isset($options['dot_noise_level'])) {
            $captcha->setDotNoiseLevel($options['dot_noise_level']);
        }
        if (isset($options['line_noise_level'])) {
            $captcha->setLineNoiseLevel($options['line_noise_level']);
        }
        if (isset($options['word_len'])) {
            $captcha->setWordlen($options['word_len']);
        }
        if (isset($options['expiration'])) {
            $captcha->setExpiration($options['expiration']);
        }
        if (isset($options['gc_freq'])) {
            $captcha->setGcFreq($options['gc_freq']);
        }
        $captcha->setImgDir($this->options['img_dir']);
        $captcha->setFont($this->options['font']);
        $captcha->setImgUrl($this->options['img_url']);

        $captcha->getWord();
        $captcha->generate();

        $this->session->set($sessionKey, $captcha->getSession()->getName());

        $view->vars = array_merge($view->vars, array(
            'img_url' => $captcha->getImgUrl().$captcha->getId().$captcha->getSuffix(),
            'height' => $captcha->getHeight(),
            'width' => $captcha->getWidth(),
            'img_alt' => $captcha->getImgAlt(),
            'img_id' => $captcha->getId(),
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults($this->options);
    }

    /**
     * @return string
     */
    public function getParent()
    {
        return TextType::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->getBlockPrefix();
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'zend_captcha';
    }
}