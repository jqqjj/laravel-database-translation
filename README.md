# laravel-database-translation

Use laravel translator with database loader or driver

## installation

```php
composer require jqqjj/laravel-database-translation
```

## migrate

```php
php artisan migrate
```

## methods

```php
Language Translation::createLanguage($code, $localName)
Language|null Translation::getLanguage($code)
Language|null Translation::getEnabledLanguage($code)
Language[] Translation::getLanguages()
Language[] Translation::getEnabledLanguages()
int Translation::enableLanguage($code)
int Translation::disableLanguage($code)
bool Translation::deleteLanguage($code)

LanguageSource Translation::createSource($key, $group = '*', $namespace = '*')
LanguageSource|null Translation::getSource($key, $group = '*', $namespace = '*')
LanguageSource[] Translation::getGroupSources($group, $namespace = '*')
LanguageSource[] Translation::getNamespaceSources($namespace)
bool Translation::deleteSource($key, $group = '*', $namespace = '*')
bool Translation::deleteGroupSources($group, $namespace = '*')
bool Translation::deleteNamespaceSources($namespace)

LanguageTranslation Translation::createTranslation($text, $language, $source)
LanguageTranslation|null Translation::getTranslation($language, $source)
array Translation::getTranslations($languageCode, $group = '*', $namespace = '*')
bool Translation::deleteTranslation($language, $source)
bool Translation::deleteGroupTranslations($language, $group, $namespace = '*')
bool Translation::deleteNamespaceTranslations($language, $namespace)
bool Translation::deleteLanguageTranslations($language)

array Translation::getGroups($namespace = '*')
array Translation::getNamespaces()
void Translation::clearCache($language, $group, $namespace = '*')
void Translation::clearCacheAll()
```
