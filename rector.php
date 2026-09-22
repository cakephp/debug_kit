<?php
declare(strict_types=1);

use Rector\Caching\ValueObject\Storage\FileCacheStorage;
use Rector\CodeQuality\Rector\BooleanNot\NegatedAndsToPositiveOrsRector;
use Rector\CodeQuality\Rector\Catch_\ThrowWithPreviousExceptionRector;
use Rector\CodeQuality\Rector\Class_\CompleteDynamicPropertiesRector;
use Rector\CodeQuality\Rector\ClassMethod\ExplicitReturnNullRector;
use Rector\CodeQuality\Rector\ClassMethod\OptionalParametersAfterRequiredRector;
use Rector\CodeQuality\Rector\Equal\UseIdenticalOverEqualWithSameTypeRector;
use Rector\CodeQuality\Rector\Foreach_\ForeachToInArrayRector;
use Rector\CodeQuality\Rector\FuncCall\CompactToVariablesRector;
use Rector\CodeQuality\Rector\FunctionLike\SimplifyUselessVariableRector;
use Rector\CodeQuality\Rector\Identical\FlipTypeControlToUseExclusiveTypeRector;
use Rector\CodeQuality\Rector\If_\ConsecutiveNullCompareReturnsToNullCoalesceQueueRector;
use Rector\CodeQuality\Rector\If_\SimplifyIfReturnBoolRector;
use Rector\CodeQuality\Rector\Include_\AbsolutizeRequireAndIncludePathRector;
use Rector\CodeQuality\Rector\Isset_\IssetOnPropertyObjectToPropertyExistsRector;
use Rector\CodeQuality\Rector\Property\FixClassCaseSensitivityVarDocblockRector;
use Rector\CodingStyle\Rector\Catch_\CatchExceptionNameMatchingTypeRector;
use Rector\CodingStyle\Rector\ClassLike\NewlineBetweenClassLikeStmtsRector;
use Rector\CodingStyle\Rector\ClassMethod\MakeInheritedMethodVisibilitySameAsParentRector;
use Rector\CodingStyle\Rector\ClassMethod\NewlineBeforeNewAssignSetRector;
use Rector\CodingStyle\Rector\FuncCall\FunctionFirstClassCallableRector;
use Rector\CodingStyle\Rector\FuncCall\StrictArraySearchRector;
use Rector\CodingStyle\Rector\FuncCall\VersionCompareFuncCallToConstantRector;
use Rector\CodingStyle\Rector\Stmt\NewlineAfterStatementRector;
use Rector\CodingStyle\Rector\String_\UseClassKeywordForClassNameResolutionRector;
use Rector\Config\RectorConfig;
use Rector\DeadCode\Rector\Assign\RemoveDoubleAssignRector;
use Rector\DeadCode\Rector\Assign\RemoveUnusedVariableAssignRector;
use Rector\DeadCode\Rector\Cast\RecastingRemovalRector;
use Rector\DeadCode\Rector\ClassMethod\RemoveDuplicatedReturnSelfDocblockRector;
use Rector\DeadCode\Rector\ClassMethod\RemoveEmptyClassMethodRector;
use Rector\DeadCode\Rector\ClassMethod\RemoveMixedDocblockOverruledByNativeTypeRector;
use Rector\DeadCode\Rector\ClassMethod\RemoveNullTagValueNodeRector;
use Rector\DeadCode\Rector\ClassMethod\RemoveParentDelegatingClassMethodRector;
use Rector\DeadCode\Rector\ClassMethod\RemoveReturnTagIncompatibleWithNativeTypeRector;
use Rector\DeadCode\Rector\ClassMethod\RemoveUnusedConstructorParamRector;
use Rector\DeadCode\Rector\ClassMethod\RemoveUnusedPrivateMethodRector;
use Rector\DeadCode\Rector\ClassMethod\RemoveUselessParamTagRector;
use Rector\DeadCode\Rector\ClassMethod\RemoveUselessReturnTagRector;
use Rector\DeadCode\Rector\ClassMethod\RemoveUselessUnionReturnDocblockRector;
use Rector\DeadCode\Rector\ConstFetch\RemovePhpVersionIdCheckRector;
use Rector\DeadCode\Rector\Expression\RemoveDeadStmtRector;
use Rector\DeadCode\Rector\For_\RemoveDeadIfForeachForRector;
use Rector\DeadCode\Rector\For_\RemoveDeadLoopRector;
use Rector\DeadCode\Rector\If_\RemoveAlwaysTrueIfConditionRector;
use Rector\DeadCode\Rector\If_\RemoveDeadInstanceOfRector;
use Rector\DeadCode\Rector\If_\UnwrapFutureCompatibleIfPhpVersionRector;
use Rector\DeadCode\Rector\MethodCall\RemoveNullArgOnNullDefaultParamRector;
use Rector\DeadCode\Rector\Node\RemoveNonExistingVarAnnotationRector;
use Rector\DeadCode\Rector\Property\RemoveDefaultValueFromAssignedPropertyRector;
use Rector\DeadCode\Rector\Property\RemoveUselessVarTagRector;
use Rector\DeadCode\Rector\StmtsAwareInterface\RemoveDeadInstanceOfAssertRector;
use Rector\DeadCode\Rector\Ternary\RemoveUselessTernaryRector;
use Rector\Php55\Rector\String_\StringClassNameToClassConstantRector;
use Rector\Php56\Rector\FuncCall\PowToExpRector;
use Rector\Php73\Rector\FuncCall\ArrayKeyFirstLastRector;
use Rector\Php73\Rector\FuncCall\SetCookieRector;
use Rector\Php73\Rector\FuncCall\StringifyStrNeedlesRector;
use Rector\Php73\Rector\String_\SensitiveHereNowDocRector;
use Rector\Php74\Rector\Closure\ClosureToArrowFunctionRector;
use Rector\Php74\Rector\Property\RestoreDefaultNullToNullableTypePropertyRector;
use Rector\Php80\Rector\FuncCall\ClassOnObjectRector;
use Rector\Php80\Rector\NotIdentical\MbStrContainsRector;
use Rector\Php81\Rector\FuncCall\NullToStrictStringFuncCallArgRector;
use Rector\Php81\Rector\Property\ReadOnlyPropertyRector;
use Rector\Php83\Rector\ClassMethod\AddOverrideAttributeToOverriddenMethodsRector;
use Rector\Set\ValueObject\SetList;
use Rector\TypeDeclaration\Rector\ArrowFunction\AddArrowFunctionReturnTypeRector;
use Rector\TypeDeclaration\Rector\BooleanAnd\BinaryOpNullableToInstanceofRector;
use Rector\TypeDeclaration\Rector\Class_\TypedPropertyFromCreateMockAssignRector;
use Rector\TypeDeclaration\Rector\ClassMethod\AddParamStringTypeFromSprintfUseRector;
use Rector\TypeDeclaration\Rector\ClassMethod\AddParamTypeBasedOnPHPUnitDataProviderRector;
use Rector\TypeDeclaration\Rector\ClassMethod\AddVoidReturnTypeWhereNoReturnRector;
use Rector\TypeDeclaration\Rector\ClassMethod\ArrayParamTypeByMethodCallTypeRector;
use Rector\TypeDeclaration\Rector\ClassMethod\BoolReturnTypeFromBooleanConstReturnsRector;
use Rector\TypeDeclaration\Rector\ClassMethod\ParamTypeByMethodCallTypeRector;
use Rector\TypeDeclaration\Rector\ClassMethod\ReturnNeverTypeRector;
use Rector\TypeDeclaration\Rector\ClassMethod\ReturnTypeFromMockObjectRector;
use Rector\TypeDeclaration\Rector\ClassMethod\ReturnTypeFromStrictFluentReturnRector;
use Rector\TypeDeclaration\Rector\ClassMethod\ReturnTypeFromStrictTypedCallRector;
use Rector\TypeDeclaration\Rector\ClassMethod\ReturnUnionTypeRector;
use Rector\TypeDeclaration\Rector\ClassMethod\ScalarParamTypeByMethodCallTypeRector;
use Rector\TypeDeclaration\Rector\ClassMethod\StrictArrayParamDimFetchRector;
use Rector\TypeDeclaration\Rector\Closure\AddClosureNeverReturnTypeRector;
use Rector\TypeDeclaration\Rector\Closure\ClosureReturnTypeFromAssertInstanceOfRector;
use Rector\TypeDeclaration\Rector\Closure\ClosureReturnTypeRector;
use Rector\TypeDeclaration\Rector\FunctionLike\AddClosureParamTypeForArrayMapRector;
use Rector\TypeDeclaration\Rector\FunctionLike\AddClosureParamTypeFromVariableCallRector;
use Rector\TypeDeclaration\Rector\Property\TypedPropertyFromAssignsRector;
use Rector\TypeDeclaration\Rector\Property\TypedPropertyFromStrictConstructorRector;
use Rector\TypeDeclaration\Rector\Property\TypedPropertyFromStrictSetUpRector;
use Rector\TypeDeclaration\Rector\StmtsAwareInterface\SafeDeclareStrictTypesRector;
use Rector\TypeDeclaration\Rector\While_\WhileNullableToInstanceofRector;

$cacheDir = getenv('RECTOR_CACHE_DIR') ?: sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'rector';

return RectorConfig::configure()
    ->withPaths([
        __DIR__ . '/config',
        __DIR__ . '/src',
        __DIR__ . '/tests',
    ])

    ->withCache(
        cacheClass: FileCacheStorage::class,
        cacheDirectory: $cacheDir,
    )

    ->withPhpSets()
    ->withAttributesSets()

    ->withSets([
        SetList::CODE_QUALITY,
        SetList::CODING_STYLE,
        SetList::DEAD_CODE,
        SetList::TYPE_DECLARATION,
    ])

    ->withSkip([
        __DIR__ . '/tests/test_app/templates',
        __DIR__ . '/tests/test_app/Plugin/TestPlugin/templates',

        ThrowWithPreviousExceptionRector::class,
        ExplicitReturnNullRector::class,
        OptionalParametersAfterRequiredRector::class,
        CompleteDynamicPropertiesRector::class,
        ForeachToInArrayRector::class,
        CompactToVariablesRector::class,
        FlipTypeControlToUseExclusiveTypeRector::class,
        ConsecutiveNullCompareReturnsToNullCoalesceQueueRector::class,
        SimplifyIfReturnBoolRector::class,
        AbsolutizeRequireAndIncludePathRector::class,
        IssetOnPropertyObjectToPropertyExistsRector::class,
        CatchExceptionNameMatchingTypeRector::class,
        CatchExceptionNameMatchingTypeRector::class,
        MakeInheritedMethodVisibilitySameAsParentRector::class,
        NewlineBeforeNewAssignSetRector::class,
        StrictArraySearchRector::class,
        VersionCompareFuncCallToConstantRector::class,
        FunctionFirstClassCallableRector::class,
        NewlineAfterStatementRector::class,
        UseClassKeywordForClassNameResolutionRector::class,
        RemoveDoubleAssignRector::class,
        RemoveUnusedVariableAssignRector::class,
        RecastingRemovalRector::class,
        RemoveEmptyClassMethodRector::class,
        RemoveNullTagValueNodeRector::class,
        RemoveUnusedConstructorParamRector::class,
        RemoveUnusedPrivateMethodRector::class,
        RemoveUselessParamTagRector::class,
        RemoveUselessReturnTagRector::class,
        RemovePhpVersionIdCheckRector::class,
        RemoveDeadStmtRector::class,
        RemoveDeadIfForeachForRector::class,
        RemoveDeadLoopRector::class,
        RemoveAlwaysTrueIfConditionRector::class,
        RemoveDeadInstanceOfRector::class,
        UnwrapFutureCompatibleIfPhpVersionRector::class,
        RemoveNullArgOnNullDefaultParamRector::class => [
            __DIR__ . '/tests/TestCase/Database/Expression/QueryExpressionTest.php',
        ],
        RemoveNonExistingVarAnnotationRector::class,
        RemoveUselessVarTagRector::class,
        PowToExpRector::class,
        ArrayKeyFirstLastRector::class,
        SetCookieRector::class,
        StringifyStrNeedlesRector::class,
        SensitiveHereNowDocRector::class,
        ClosureToArrowFunctionRector::class,
        ReadOnlyPropertyRector::class,
        AddArrowFunctionReturnTypeRector::class,
        BinaryOpNullableToInstanceofRector::class,
        NewlineBetweenClassLikeStmtsRector::class,
        AddParamStringTypeFromSprintfUseRector::class,
        AddParamTypeBasedOnPHPUnitDataProviderRector::class,
        AddVoidReturnTypeWhereNoReturnRector::class,
        BoolReturnTypeFromBooleanConstReturnsRector::class,
        ParamTypeByMethodCallTypeRector::class,
        ReturnNeverTypeRector::class,
        ReturnTypeFromMockObjectRector::class,
        ReturnTypeFromStrictFluentReturnRector::class,
        ReturnTypeFromStrictTypedCallRector::class,
        ReturnUnionTypeRector::class,
        StrictArrayParamDimFetchRector::class,
        TypedPropertyFromCreateMockAssignRector::class,
        AddClosureNeverReturnTypeRector::class,
        ClosureReturnTypeRector::class,
        TypedPropertyFromAssignsRector::class,
        TypedPropertyFromStrictConstructorRector::class,
        TypedPropertyFromStrictSetUpRector::class,
        WhileNullableToInstanceofRector::class,
        RestoreDefaultNullToNullableTypePropertyRector::class,

        // Manual - only appliable for part of the code
        UseIdenticalOverEqualWithSameTypeRector::class,
        RemoveDeadStmtRector::class,
        StringClassNameToClassConstantRector::class,
        ArrayKeyFirstLastRector::class,
        ClassOnObjectRector::class,

        // Newly aggressive in rector 2.4 - keep the bump behavior-neutral:
        // adds declare(strict_types=1) to test fixtures/config (out of scope here),
        SafeDeclareStrictTypesRector::class,
        // and rewrites `$x ?: []` in ways that can change behavior on undefined/empty values.
        RemoveUselessTernaryRector::class,

        // New in rector 2.5 - skipped to keep the version bump behavior-neutral.
        // Together these touch ~226 files, mostly docblock removal. Whether to apply
        // them is a separate decision from getting CI green again.
        NegatedAndsToPositiveOrsRector::class,
        FixClassCaseSensitivityVarDocblockRector::class,
        RemoveDuplicatedReturnSelfDocblockRector::class,
        RemoveMixedDocblockOverruledByNativeTypeRector::class,
        RemoveParentDelegatingClassMethodRector::class,
        RemoveReturnTagIncompatibleWithNativeTypeRector::class,
        RemoveUselessUnionReturnDocblockRector::class,
        RemoveDefaultValueFromAssignedPropertyRector::class,
        RemoveDeadInstanceOfAssertRector::class,
        MbStrContainsRector::class,
        ArrayParamTypeByMethodCallTypeRector::class,
        ScalarParamTypeByMethodCallTypeRector::class,
        ClosureReturnTypeFromAssertInstanceOfRector::class,
        AddClosureParamTypeForArrayMapRector::class,
        AddClosureParamTypeFromVariableCallRector::class,
    ]);
