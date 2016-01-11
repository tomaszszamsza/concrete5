<?php
namespace Concrete\Core\Express\Form;

use Concrete\Core\Application\Application;
use Concrete\Core\Entity\Express\FieldSet;
use Concrete\Core\Entity\Express\Form;
use Concrete\Core\Express\BaseEntity;
use Doctrine\ORM\EntityManagerInterface;

class ViewRenderer implements RendererInterface
{
    protected $entityManager;
    protected $application;

    public function __construct(Application $application, EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->application = $application;
    }

    protected function getFormOpenTag()
    {
        return '<div class="ccm-express-form">';
    }

    protected function getFormCloseTag()
    {
        return '</div>';
    }

    protected function getFieldSetOpenTag(FieldSet $set)
    {
        $html = '<fieldset class="ccm-express-form-field-set">';
        if ($set->getTitle()) {
            $html .= '<legend>' . $set->getTitle() . '</legend>';
        }

        return $html;
    }

    protected function getFieldSetCloseTag()
    {
        return '</fieldset>';
    }

    protected function renderFieldSet(FieldSet $fieldSet, BaseEntity $entity)
    {
        $html = $this->getFieldSetOpenTag($fieldSet);
        foreach ($fieldSet->getControls() as $control) {
            $factory = new RendererFactory($control, $this->application, $this->entityManager);
            $renderer = $factory->getViewRenderer($entity);
            if (is_object($renderer)) {
                $html .= $renderer->render();
            }
        }
        $html .= $this->getFieldSetCloseTag();

        return $html;
    }

    public function render(Form $form, BaseEntity $entity = null)
    {
        $html = $this->getFormOpenTag();
        foreach ($form->getFieldSets() as $fieldSet) {
            $html .= $this->renderFieldSet($fieldSet, $entity);
        }

        $html .= $this->getFormCloseTag();

        return $html;
    }
}
