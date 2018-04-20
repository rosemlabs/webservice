<?php

namespace Rosem\Access\GraphQL\Mutations;

use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Psrnext\GraphQL\{
    AbstractQuery, TypeRegistryInterface
};
use Rosem\Access\GraphQL\Types\UserType;

class UpdateUserMutation extends AbstractQuery
{
    public function getDescription(): string
    {
        return 'Update the user';
    }

    public function getType(TypeRegistryInterface $typeRegistry)
    {
//        return $typeRegistry->notNull($typeRegistry->get(UserType::class));
        return Type::nonNull($typeRegistry->get(UserType::class));
    }

    public function getDefaultArguments(TypeRegistryInterface $typeRegistry): array
    {
        return [
            'id'        => Type::id(),
            'email'     => Type::string(),
            'firstName' => Type::string(),
            'lastName'  => Type::string(),
            'password'  => Type::string(),
        ];
    }

    public function resolve($source, $args, $context, ResolveInfo $info)
    {
//        /** @var \Rosem\Access\Entity\User $user */
//        $user = $this->mapper->find($args['id']);
//        unset($args['id']);
//        $user->fill($args);
//        try {
//            $this->mapper->store($user);
//        } catch (\Exception $e) {
//            echo $e->getMessage();
//        }
//
//        return $user;

        return [
            'id'        => 0,
            'firstName' => 'SUCCESS',
        ];
    }
}