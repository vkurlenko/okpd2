import { NgModule } from '@angular/core';
import { BrowserModule } from '@angular/platform-browser';
import { FormsModule, ReactiveFormsModule } from '@angular/forms';
import { BrowserAnimationsModule } from '@angular/platform-browser/animations';
import { AppRoutingModule } from './app-routing.module';
import { AppComponent } from './app.component';
import { TreeModule } from "primeng/tree";
import { HttpClientModule } from '@angular/common/http';
import { NodeService } from "../api/services/node.service";
import { ButtonModule } from 'primeng/button';
import { RouterModule } from "@angular/router";
import { ContextMenuModule } from "primeng/contextmenu";
import { DialogModule } from "primeng/dialog";
import { InputTextModule } from "primeng/inputtext";
import { FileUploadModule } from "primeng/fileupload";
import { ConfirmationService } from "primeng/api";
import { MessageService } from "primeng/api";
import { ToastModule } from "primeng/toast";
import { ConfirmDialogModule } from "primeng/confirmdialog";

@NgModule({
  declarations: [
    AppComponent
  ],
  imports: [
    BrowserModule,
    AppRoutingModule,
    TreeModule,
    HttpClientModule,
    ButtonModule,
    FormsModule,
    RouterModule.forRoot([
      { path: '', component: AppComponent }
    ]),
    ContextMenuModule,
    DialogModule,
    BrowserAnimationsModule,
    ReactiveFormsModule,
    InputTextModule,
    FileUploadModule,
    ToastModule,
    ConfirmDialogModule
  ],
  providers: [NodeService, ConfirmationService, MessageService],
  bootstrap: [AppComponent]
})
export class AppModule { }
