import app from 'flarum/admin/app';

app.initializers.add('blomstra/s3-assets', () => {
  app.extensionData
    .for('blomstra-s3-assets')
    .registerSetting({
      setting: 'fof-upload.awsS3Key',
      type: 'string',
      label: app.translator.trans('blomstra-s3-assets.admin.settings.s3key.label'),
      help: app.translator.trans('blomstra-s3-assets.admin.settings.s3key.help'),
    })
    .registerSetting({
      setting: 'fof-upload.awsS3Secret',
      type: 'string',
      label: app.translator.trans('blomstra-s3-assets.admin.settings.s3secret.label'),
      help: app.translator.trans('blomstra-s3-assets.admin.settings.s3secret.help'),
    })
    .registerSetting({
      setting: 'fof-upload.awsS3Region',
      type: 'string',
      label: app.translator.trans('blomstra-s3-assets.admin.settings.s3region.label'),
      help: app.translator.trans('blomstra-s3-assets.admin.settings.s3region.help'),
    })
    .registerSetting({
      setting: 'fof-upload.awsS3Bucket',
      type: 'string',
      label: app.translator.trans('blomstra-s3-assets.admin.settings.s3bucket.label'),
      help: app.translator.trans('blomstra-s3-assets.admin.settings.s3bucket.help'),
    })
    .registerSetting({
      setting: 'fof-upload.cdnUrl',
      type: 'string',
      label: app.translator.trans('blomstra-s3-assets.admin.settings.s3url.label'),
      help: app.translator.trans('blomstra-s3-assets.admin.settings.s3url.help'),
    })
    .registerSetting({
      setting: 'fof-upload.awsS3Endpoint',
      type: 'string',
      label: app.translator.trans('blomstra-s3-assets.admin.settings.s3endpoint.label'),
      help: app.translator.trans('blomstra-s3-assets.admin.settings.s3endpoint.help'),
    })
    .registerSetting({
      setting: 'fof-upload.awsS3UsePathStyleEndpoint',
      type: 'boolean',
      label: app.translator.trans('blomstra-s3-assets.admin.settings.s3path-style-endpoint.label'),
      help: app.translator.trans('blomstra-s3-assets.admin.settings.s3path-style-endpoint.help'),
    });
});
