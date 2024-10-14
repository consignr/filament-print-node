# filament-print-node
 A collection of filament resources to manage your printnode account from the admin panel

## Usage
The plugin integrates [PrintNode](https://www.printnode.com/en) functionality in your filament panel(s), allowing admins to quickly access Computer, Printer and PrintJob resources. It provides custom actions to send print requests to the [PrintNode](https://www.printnode.com/en/docs/api/curl) api.
 
### Registering the plugin

```php
use Consignr\FilamentPrintNode\FilamentPrintNodePlugin;

public function panel(Panel $panel): Panel
{
    return $panel
        ->plugins([
            FilamentPrintNodePlugin::make(),
        ])
}
```

### Sending PrintJob requests
Add the the custom table action to your desired resource

```php
use Consignr\FilamentPrintNode\Actions\CreatePrintJobAction;
use Consignr\FilamentPrintNode\Enums\ContentType;

public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
            ])
            ->filters([
                //
            ])
            ->actions([
                CreatePrintJobAction::make()
                    ->printerId(1234567) // Provide the ID for the printer
                    ->contentType(ContentType::PdfBase64) // Provide the content type PdfUri | PdfBase64 | RawUri | RawBase64
                    ->content(function (Model $record): string {

                        if (Storage::disk('public')->exists($record->path)) {
                            return base64_encode(Storage::disk('public')->get($record->path))
                        }

                        return;
                        
                    }) // Uri where the document can be downloaded or base64 encoded document
            ]);
    }
```