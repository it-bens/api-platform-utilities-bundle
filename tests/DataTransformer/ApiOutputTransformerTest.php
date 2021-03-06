<?php

declare(strict_types=1);

namespace ITB\ApiPlatformUtilitiesBundle\Tests\DataTransformer;

use ITB\ApiPlatformUtilitiesBundle\DataTransformer\ApiOutputTransformer;
use ITB\ApiPlatformUtilitiesBundle\DataTransformer\InvalidObjectType;
use ITB\ApiPlatformUtilitiesBundle\DataTransformer\InvalidResponseType;
use ITB\ApiPlatformUtilitiesBundle\Tests\ITBApiPlatformUtilitiesKernel;
use ITB\ApiPlatformUtilitiesBundle\Tests\Mock\Object1;
use ITB\ApiPlatformUtilitiesBundle\Tests\Mock\Object2;
use ITB\ApiPlatformUtilitiesBundle\Tests\Mock\Object3;
use ITB\ObjectTransformer\TransformationMediatorInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Yaml\Yaml;

final class ApiOutputTransformerTest extends TestCase
{
    private const INVALID_CONFIGURATION_FILES = [
        'output_transformation_without_object_class' => __DIR__ . '/../Fixtures/BundleConfiguration/config_invalid_output_transformations_1.yml',
        'output_transformation_with_invalid_object_class' => __DIR__ . '/../Fixtures/BundleConfiguration/config_invalid_output_transformations_2.yml',
        'output_transformation_without_response_class' => __DIR__ . '/../Fixtures/BundleConfiguration/config_invalid_output_transformations_3.yml',
        'output_transformation_with_invalid_response_class' => __DIR__ . '/../Fixtures/BundleConfiguration/config_invalid_output_transformations_4.yml'
    ];

    /** @var ApiOutputTransformer $apiOutputTransformer */
    private $apiOutputTransformer;
    /** @var TransformationMediatorInterface $transformationMediator */
    private $transformationMediator;

    public function setUp(): void
    {
        $config = Yaml::parseFile(__DIR__ . '/../Fixtures/BundleConfiguration/config_valid.yml');
        $kernel = new ITBApiPlatformUtilitiesKernel('test', true, $config);
        $kernel->boot();
        $container = $kernel->getContainer();

        /** @phpstan-ignore-next-line */
        $this->apiOutputTransformer = $container->get('itb_api_platform_utilities.api_output_transformer');
        /** @phpstan-ignore-next-line */
        $this->transformationMediator = $container->get('itb_object_transformer.transformation_mediator');
    }

    public function testConstructionInvalidTransformationWithInvalidObjectClass(): void
    {
        $this->setOutputCallback(static function () {
        });

        $config = Yaml::parseFile(self::INVALID_CONFIGURATION_FILES['output_transformation_with_invalid_object_class']);
        $this->expectExceptionObject(new InvalidObjectType('\'Blub.\' is not a valid Object type.'));

        new ApiOutputTransformer($config['output_transformations'], $this->transformationMediator);
    }

    public function testConstructionInvalidTransformationWithInvalidResponseClass(): void
    {
        $this->setOutputCallback(static function () {
        });

        $config = Yaml::parseFile(
            self::INVALID_CONFIGURATION_FILES['output_transformation_with_invalid_response_class']
        );
        $this->expectExceptionObject(new InvalidResponseType('\'Blub.\' is not a valid Response type.'));

        new ApiOutputTransformer($config['output_transformations'], $this->transformationMediator);
    }

    public function testConstructionInvalidTransformationWithoutObjectClass(): void
    {
        $this->setOutputCallback(static function () {
        });

        $config = Yaml::parseFile(self::INVALID_CONFIGURATION_FILES['output_transformation_without_object_class']);
        $this->expectExceptionObject(new InvalidObjectType('\'null\' is not a valid Object type.'));

        new ApiOutputTransformer($config['output_transformations'], $this->transformationMediator);
    }

    public function testConstructionInvalidTransformationWithoutResponseClass(): void
    {
        $this->setOutputCallback(static function () {
        });

        $config = Yaml::parseFile(self::INVALID_CONFIGURATION_FILES['output_transformation_without_response_class']);
        $this->expectExceptionObject(new InvalidResponseType('\'null\' is not a valid Response type.'));

        new ApiOutputTransformer($config['output_transformations'], $this->transformationMediator);
    }

    public function testSupportsTransformation(): void
    {
        $request = new Object2(5);
        $context['resource_class'] = Object2::class;

        $supportsTransformation = $this->apiOutputTransformer->supportsTransformation(
            $request,
            Object1::class,
            $context
        );
        $this->assertTrue($supportsTransformation);
    }

    public function testSupportsTransformationNot(): void
    {
        $request = new Object3('I\'ll be back!');
        $context['resource_class'] = Object3::class;

        $supportsTransformation = $this->apiOutputTransformer->supportsTransformation(
            $request,
            Object1::class,
            $context
        );
        $this->assertFalse($supportsTransformation);
    }

    public function testTransform(): void
    {
        $request = new Object2(5);
        $context = [];

        $object = $this->apiOutputTransformer->transform($request, Object1::class, $context);
        $this->assertInstanceOf(Object1::class, $object);
    }
}
