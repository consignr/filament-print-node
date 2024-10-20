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

Configure the navigation settings for print node cluster by chaining additonal methods after `make()`.

```php
use Consignr\FilamentPrintNode\FilamentPrintNodePlugin;

public function panel(Panel $panel): Panel
{
    return $panel
        ->plugins([
            FilamentPrintNodePlugin::make()
                ->navigationLabel('Printing') // Set the label for the cluster
                ->navigationGroup('Settings') // Set the group for the cluster
                ->navigationIcon('heroicon-o-printer') // Set the icon for the cluster
                ->navigationSort(3) // Set sort order for the cluster
        ])
}
```

Further configuration options can be applied for each resource. The below example configures the computer 
resource using the `->computerResource()` method. Equivelant methods exist for the other resources, `->printerResource()`
and `->printJobResource()`.

```php
use Consignr\FilamentPrintNode\FilamentPrintNodePlugin;

public function panel(Panel $panel): Panel
{
    return $panel
        ->plugins([
            FilamentPrintNodePlugin::make()
                ->computerResource(
                    label: 'Device',
                    pluralLabel: 'Devices',
                    navigationLabel: 'Tablets',
                    navigationIcon: 'heroicon-o-device-tablet',
                    navigationSort: 2,
                    navigationBadgeCount: fn (Computer $record): int => $record->count()
                )
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
                    ->title() // Provide a title to be given to the print job. This is the name which will appear in the operating system's print queue.
                    ->source() // Provide a text description of how the print job was created or where the print job originated.
                    ->expiresAfter() // Set the the maximum number of seconds PrintNode should retain this print job in the event that the print job cannot be printed immediately. Defaults to 14 days or 1209600 seconds
                    ->quantity() // A positive integer specifying the number of times this print job should be delivered to the print queue.
            ]);
    }
```