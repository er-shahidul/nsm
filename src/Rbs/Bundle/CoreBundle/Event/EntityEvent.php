<?php
namespace Rbs\Bundle\CoreBundle\Event;

use Symfony\Component\PropertyAccess\Exception\NoSuchPropertyException;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Serializer;

class EntityEvent extends BaseEvent
{
    protected $candidateProperties = array('username', 'name');

    protected $propertiesFound = array();

    /**
     * @var
     */
    protected $entity;
    /**
     * @var
     */
    protected $eventName;

    protected $changesValues = array();

    /** @var  Serializer */
    protected $serializer;

    public function __construct($entity, $values)
    {
        $this->entity = $entity;
        $this->changesValues = $values;

        $encoder = array(new XmlEncoder(), new JsonEncoder());
        $normalize = array(new GetSetMethodNormalizer());

        $this->serializer = new Serializer($normalize, $encoder);
    }

    /**
     * @param $eventName
     *
     * @return array
     */
    public function getEventLogInfo($eventName)
    {
        if (null == $this->changesValues) {
            //return null;
        }

        $this->eventName = $eventName;
        $reflectionClass = $this->getReflectionClassFromObject($this->entity);

        $typeName = $this->getTypeName($reflectionClass);
        $eventType = $this->getEventType($typeName);
        $eventDescription = $this->getDescriptionString($reflectionClass, $typeName);

        return array(
            'description' => $eventDescription,
            'type' => $eventType,
        );
    }

    /**
     * @param string $typeName
     * @return string
     */
    protected function getEventType($typeName)
    {
        return $typeName . " " . $this->getEventShortName();
    }

    protected function getTypeName(\ReflectionClass $reflectionClass)
    {
        return $reflectionClass->getShortName();
    }

    protected function getDescriptionString(\ReflectionClass $reflectionClass, $typeName)
    {
        $property = $this->getBestCandidatePropertyForIdentify($reflectionClass);

        $descriptionTemplate = '%s %s request is generate for Verification successfully ';

        if ($property) {
            $humanReadable = preg_replace("/(([a-z])([A-Z])|([A-Z])([A-Z][a-z]))/","\\2\\4 \\3\\5", $typeName);
            $descriptionTemplate .= sprintf(' %s = "%s" ', $humanReadable, $this->getProperty(strtolower($property)));
        }

        switch ($this->getEventShortName()){
            case 'updated':
                $descriptionTemplate .= " old values = %s, new values = %s"; break;
            case 'deleted':
                $descriptionTemplate .= " old values = %s"; break;
            case 'created':
            default:
                $descriptionTemplate .= " with values = %s"; break;
        }

        return sprintf($descriptionTemplate,
            $typeName,
            substr($this->getEventShortName(), 0, -1),
            $this->jsonToCommaSeparatedKeyValue($this->getJsonSerialized($this->entity)),
            $this->jsonToCommaSeparatedKeyValue($this->getJsonSerialized($this->changesValues))
        );
    }

    /**
     * @param \ReflectionClass $reflectionClass
     * @return null|string
     */
    protected function getBestCandidatePropertyForIdentify(\ReflectionClass $reflectionClass)
    {
        $properties = $reflectionClass->getProperties(
            \ReflectionProperty::IS_PROTECTED | \ReflectionProperty::IS_PRIVATE | \ReflectionProperty::IS_PUBLIC
        );

        return $this->getNameOrIdPropertyFromPropertyList($properties,
            strtolower($reflectionClass->getShortName()) . "id"
        );
    }

    /**
     * @param $properties
     * @param $entityIdStr
     * @return null|string
     */
    private function getNameOrIdPropertyFromPropertyList($properties, $entityIdStr)
    {
        $propertyName = null;

        foreach ($properties as $property) {
            $propertyNameLower = strtolower($property->name);

            if (null !== $foundPropertyName = $this->getPropertyNameInCandidateList($propertyNameLower, $property)) {
                return $foundPropertyName;
            }

            if (null === $propertyName && $this->isIdProperty($propertyNameLower, $entityIdStr)) {
                $this->propertiesFound['id'] = $propertyName = $property->name;
            }
        }

        return $propertyName;
    }

    /**
     * @param string $propertyName
     * @param \ReflectionProperty $property
     * @return null | string
     */
    protected function getPropertyNameInCandidateList($propertyName, \ReflectionProperty $property)
    {
        foreach ($this->candidateProperties as $candidate) {
            if ($propertyName == $candidate) {
                return $this->propertiesFound[$candidate] = $property->name;
            }
        }

        return null;
    }


    /**
     * @param string $name
     * @return string|mixed
     */
    protected function getProperty($name)
    {
        $propertyAccessor = PropertyAccess::createPropertyAccessor();

        try {
            return $propertyAccessor->getValue($this->entity, $this->propertiesFound[$name]);
        } catch (NoSuchPropertyException $e) {
            return '{INACCESSIBLE} property! ' . $e->getMessage();
        }
    }

    /**
     * @param string $property
     * @return bool
     */
    protected function isIdProperty($property)
    {
        return $property == 'id' || $property == 'iid';
    }

    /**
     * @return string
     */
    protected function getEventShortName()
    {
        return substr(strrchr($this->eventName, '.'), 1);
    }

    protected function getReflectionClassFromObject($object)
    {
        return new \ReflectionClass(get_class($object));
    }

    protected function getJsonSerialized($entity, $encoded = true)
    {
        $values = array();
        $fields = json_decode($this->serializer->serialize($entity, 'json'));
        foreach ($fields as $field => $value) {
            $fieldName = $this->humanize($field);//ucfirst(trim(strtolower(preg_replace(array('/([A-Z])/', '/[_\s]+/'), array('_$1', ' '), $field))));

            switch ($field) {
                case 'roleString':
                    unset($values[$field]);
                    //$values[$fieldName] = implode(", ", (array)$value);
                    break;
                case 'roles':
                    if (is_object($value)) {
                        $value = (array)$value;
                    } elseif (!is_array($value)) {
                        $value = unserialize($value);
                    }
                    $values[$fieldName] = implode(", ", $value);
                    break;
                default:
                    $values[$fieldName] = $value;
            }
        }

        if (!$encoded) {
            return $values;
        }

        return json_encode($values);
    }

    protected function jsonToCommaSeparatedKeyValue($json)
    {
        $output = array();
        $data = json_decode($json);
        foreach ($data as $key => $value) {
            if (is_string($value)) {
                $output[] = $key . ": " . $value;
            }
        }

        return implode(", ", $output);
    }

    public function prepareDiffs($delta) {
        $old = $this->getJsonSerialized($this->entity, false);
        $new = $this->getJsonSerialized($this->changesValues, false);

        $output = array();
        foreach ($delta as $field => $value) {
            $field = $this->humanize($field);
            $output['old'][$field] = $old[$field];
            $output['new'][$field] = $new[$field];
        }

        return $output;
    }
}