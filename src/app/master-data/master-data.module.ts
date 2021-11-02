import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { TableComponent } from './table/table.component';
import { MasterTableComponent } from './master-table/master-table.component';



@NgModule({
  declarations: [
    TableComponent,
    MasterTableComponent
  ],
  imports: [
    CommonModule
  ]
})
export class MasterDataModule { }
