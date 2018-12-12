# Install
**Add to composer.json**
```
    "repositories": [
        {
            "type": "vcs",
            "url":  "https://github.com/bezdelnique/yii2-app"
        }
    ],
```

**Run**
```
composer require bezdelnique/yii2-app
```


# Force project update
```
rm -Rf vendor/bezdelnique/yii2-app
composer install
```

# Move tag
git tag
git tag -f v1.0.5
git push origin --tags -f



# Run test
```
./vendor/bin/codecept run
```

