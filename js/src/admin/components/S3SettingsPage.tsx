import app from 'flarum/admin/app';
import ExtensionPage from 'flarum/admin/components/ExtensionPage';

export default class S3SettingsPage extends ExtensionPage {
  content() {
    const s3SetByEnv = app.data.s3SetByEnv;

    return (
      <div className="ExtensionPage--settings">
        <div className="container">
          {s3SetByEnv ? (
            <h3>{app.translator.trans('blomstra-s3-assets.admin.settings.configured_by_environment')}</h3>
          ) : (
            <div className="Form-group">
              <h3>{app.translator.trans('blomstra-s3-assets.admin.settings.aws-section')}</h3>
              {this.buildSettingComponent({
                setting: 'fof-upload.awsS3Key',
                type: 'string',
                label: app.translator.trans('blomstra-s3-assets.admin.settings.s3key.label'),
                help: app.translator.trans('blomstra-s3-assets.admin.settings.s3key.help'),
              })}
              {this.buildSettingComponent({
                setting: 'fof-upload.awsS3Secret',
                type: 'string',
                label: app.translator.trans('blomstra-s3-assets.admin.settings.s3secret.label'),
                help: app.translator.trans('blomstra-s3-assets.admin.settings.s3secret.help'),
              })}
              {this.buildSettingComponent({
                setting: 'fof-upload.awsS3Region',
                type: 'string',
                label: app.translator.trans('blomstra-s3-assets.admin.settings.s3region.label'),
                help: app.translator.trans('blomstra-s3-assets.admin.settings.s3region.help'),
              })}
              {this.buildSettingComponent({
                setting: 'fof-upload.awsS3Bucket',
                type: 'string',
                label: app.translator.trans('blomstra-s3-assets.admin.settings.s3bucket.label'),
                help: app.translator.trans('blomstra-s3-assets.admin.settings.s3bucket.help'),
              })}
              <h3>{app.translator.trans('blomstra-s3-assets.admin.settings.s3-compatible-section')}</h3>
              <p className="helpText">{app.translator.trans('blomstra-s3-assets.admin.settings.s3-compatible-section-help')}</p>
              {this.buildSettingComponent({
                setting: 'fof-upload.cdnUrl',
                type: 'string',
                label: app.translator.trans('blomstra-s3-assets.admin.settings.s3url.label'),
                help: app.translator.trans('blomstra-s3-assets.admin.settings.s3url.help'),
              })}
              {this.buildSettingComponent({
                setting: 'fof-upload.awsS3Endpoint',
                type: 'string',
                label: app.translator.trans('blomstra-s3-assets.admin.settings.s3endpoint.label'),
                help: app.translator.trans('blomstra-s3-assets.admin.settings.s3endpoint.help'),
              })}
              {this.buildSettingComponent({
                setting: 'fof-upload.awsS3UsePathStyleEndpoint',
                type: 'boolean',
                label: app.translator.trans('blomstra-s3-assets.admin.settings.s3path-style-endpoint.label'),
                help: app.translator.trans('blomstra-s3-assets.admin.settings.s3path-style-endpoint.help'),
              })}
              {this.submitButton()}
            </div>
          )}
        </div>
      </div>
    );
  }
}
