import app from 'flarum/admin/app';
import S3SettingsPage from './components/S3SettingsPage';

app.initializers.add('blomstra/s3-assets', () => {
  app.extensionData.for('blomstra-s3-assets').registerPage(S3SettingsPage);
});
