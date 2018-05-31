# blogs
examPAL blog

Deploy commands:

```
sudo rm -r /var/www/html/wp-content/plugins/exampal-study-plan.back
sudo mv /var/www/html/wp-content/plugins/exampal-study-plan /var/www/html/wp-content/plugins/exampal-study-plan.back
sudo cp -r /var/www/blogs/gmat/plugins/exampal-study-plan/ /var/www/html/wp-content/plugins/exampal-study-plan

sudo rm -r /var/www/html/wp-content/themes/exampal_blog.back
sudo mv /var/www/html/wp-content/themes/exampal_blog var/www/html/wp-content/themes/exampal_blog.back
sudo cp -r /var/www/blogs/gmat/themes/exampal_blog/ /var/www/html/wp-content/themes/exampal_blog
```
