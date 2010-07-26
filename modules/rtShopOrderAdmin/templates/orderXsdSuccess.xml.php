<?xml version="1.0" encoding="UTF-8"?>
<xs:schema xmlns:xs="http://www.w3.org/2001/XMLSchema" elementFormDefault="qualified" targetNamespace="http://www.reditype.com" xmlns:xsi="<?php echo url_for('@rt_shop_order_xsd_download?sf_format=xml',true) ?>" xmlns:ns1="http://www.reditype.com">
  <xs:import namespace="<?php echo url_for('@rt_shop_order_xsd_download?sf_format=xml',true) ?>" schemaLocation="xsi.xsd"/>
	<xs:element name="orderReport">
		<xs:complexType>
			<xs:sequence>
				<xs:element maxOccurs="unbounded" ref="ns1:order"/>
			</xs:sequence>
			<xs:attribute name="elementFormDefault" use="required" type="xs:NCName"/>
			<xs:attribute ref="xsi:schemaLocation" use="required"/>
		</xs:complexType>
	</xs:element>
	<xs:element name="order">
		<xs:complexType>
			<xs:sequence>
				<xs:element ref="ns1:id"/>
				<xs:element ref="ns1:reference"/>
				<xs:element ref="ns1:status"/>
				<xs:element ref="ns1:is_wholesale"/>
				<xs:element ref="ns1:email_address"/>
				<xs:element ref="ns1:user_id"/>
				<xs:element ref="ns1:shipping_charge"/>
				<xs:element ref="ns1:taxes"/>
				<xs:element ref="ns1:promotions"/>
				<xs:element ref="ns1:vouchers"/>
				<xs:element ref="ns1:products"/>
				<xs:element ref="ns1:items_charge"/>
				<xs:element ref="ns1:total_charge"/>
				<xs:element ref="ns1:payment"/>
				<xs:element ref="ns1:notes_user"/>
				<xs:element ref="ns1:notes_admin"/>
				<xs:element ref="ns1:created_at"/>
				<xs:element ref="ns1:updated_at"/>
			</xs:sequence>
		</xs:complexType>
	</xs:element>
	<xs:element name="reference" type="xs:NMTOKEN"/>
	<xs:element name="status" type="xs:NCName"/>
	<xs:element name="is_wholesale">
		<xs:complexType/>
	</xs:element>
	<xs:element name="email_address" type="xs:string"/>
	<xs:element name="user_id">
		<xs:complexType/>
	</xs:element>
	<xs:element name="shipping_charge" type="xs:decimal"/>
	<xs:element name="taxes">
		<xs:complexType>
			<xs:sequence>
				<xs:element ref="ns1:tax"/>
			</xs:sequence>
		</xs:complexType>
	</xs:element>
	<xs:element name="tax">
		<xs:complexType>
			<xs:sequence>
				<xs:element ref="ns1:charge"/>
				<xs:element ref="ns1:component"/>
				<xs:element ref="ns1:mode"/>
				<xs:element ref="ns1:rate"/>
			</xs:sequence>
		</xs:complexType>
	</xs:element>
	<xs:element name="charge" type="xs:decimal"/>
	<xs:element name="component" type="xs:decimal"/>
	<xs:element name="rate" type="xs:decimal"/>
	<xs:element name="promotions">
		<xs:complexType>
			<xs:sequence>
				<xs:element ref="ns1:promotion"/>
			</xs:sequence>
		</xs:complexType>
	</xs:element>
	<xs:element name="promotion">
		<xs:complexType>
			<xs:sequence>
				<xs:element ref="ns1:id"/>
				<xs:element ref="ns1:reduction"/>
				<xs:element ref="ns1:data"/>
			</xs:sequence>
		</xs:complexType>
	</xs:element>
	<xs:element name="vouchers">
		<xs:complexType>
			<xs:sequence>
				<xs:element minOccurs="0" ref="ns1:voucher"/>
			</xs:sequence>
		</xs:complexType>
	</xs:element>
	<xs:element name="voucher">
		<xs:complexType>
			<xs:sequence>
				<xs:element ref="ns1:id"/>
				<xs:element ref="ns1:reduction"/>
				<xs:element ref="ns1:code"/>
				<xs:element ref="ns1:data"/>
			</xs:sequence>
		</xs:complexType>
	</xs:element>
	<xs:element name="products">
		<xs:complexType>
			<xs:sequence>
				<xs:element maxOccurs="unbounded" ref="ns1:product"/>
			</xs:sequence>
		</xs:complexType>
	</xs:element>
	<xs:element name="product">
		<xs:complexType>
			<xs:sequence>
				<xs:element ref="ns1:data"/>
			</xs:sequence>
		</xs:complexType>
	</xs:element>
	<xs:element name="items_charge" type="xs:decimal"/>
	<xs:element name="total_charge" type="xs:decimal"/>
	<xs:element name="payment">
		<xs:complexType/>
	</xs:element>
	<xs:element name="notes_user">
		<xs:complexType/>
	</xs:element>
	<xs:element name="notes_admin">
		<xs:complexType/>
	</xs:element>
	<xs:element name="id" type="xs:integer"/>
	<xs:element name="mode" type="xs:string"/>
	<xs:element name="reduction" type="xs:decimal"/>
	<xs:element name="data">
		<xs:complexType>
			<xs:sequence>
				<xs:element ref="ns1:id"/>
				<xs:choice>
					<xs:sequence>
						<xs:element ref="ns1:id_product"/>
						<xs:element ref="ns1:sku"/>
						<xs:element ref="ns1:sku_product"/>
					</xs:sequence>
					<xs:sequence>
						<xs:element ref="ns1:stackable"/>
						<xs:element ref="ns1:date_from"/>
						<xs:element ref="ns1:date_to"/>
						<xs:element ref="ns1:reduction_type"/>
						<xs:element ref="ns1:reduction_value"/>
					</xs:sequence>
				</xs:choice>
				<xs:element ref="ns1:title"/>
				<xs:choice>
					<xs:sequence>
						<xs:element ref="ns1:comment"/>
						<xs:element ref="ns1:type"/>
						<xs:element ref="ns1:code"/>
						<xs:element ref="ns1:batch_reference"/>
						<xs:element ref="ns1:count"/>
						<xs:element ref="ns1:mode"/>
						<xs:element ref="ns1:quantity_from"/>
						<xs:element ref="ns1:quantity_to"/>
						<xs:element ref="ns1:total_from"/>
						<xs:element ref="ns1:total_to"/>
						<xs:element ref="ns1:created_at"/>
						<xs:element ref="ns1:updated_at"/>
					</xs:sequence>
					<xs:sequence>
						<xs:element ref="ns1:variations"/>
						<xs:element ref="ns1:summary"/>
						<xs:element ref="ns1:quantity"/>
						<xs:element ref="ns1:charge_price"/>
						<xs:element ref="ns1:price_promotion"/>
						<xs:element ref="ns1:price_retail"/>
						<xs:element ref="ns1:price_wholesale"/>
						<xs:element ref="ns1:currency"/>
					</xs:sequence>
				</xs:choice>
			</xs:sequence>
		</xs:complexType>
	</xs:element>
	<xs:element name="id_product" type="xs:integer"/>
	<xs:element name="sku" type="xs:NCName"/>
	<xs:element name="sku_product" type="xs:string"/>
	<xs:element name="stackable" type="xs:string"/>
	<xs:element name="date_from">
		<xs:complexType/>
	</xs:element>
	<xs:element name="date_to">
		<xs:complexType/>
	</xs:element>
	<xs:element name="reduction_type" type="xs:NCName"/>
	<xs:element name="reduction_value" type="xs:decimal"/>
	<xs:element name="title" type="xs:string"/>
	<xs:element name="comment">
		<xs:complexType/>
	</xs:element>
	<xs:element name="type" type="xs:NCName"/>
	<xs:element name="batch_reference" type="xs:string"/>
	<xs:element name="count" type="xs:integer"/>
	<xs:element name="quantity_from">
		<xs:complexType/>
	</xs:element>
	<xs:element name="quantity_to">
		<xs:complexType/>
	</xs:element>
	<xs:element name="total_from">
		<xs:complexType/>
	</xs:element>
	<xs:element name="total_to">
		<xs:complexType/>
	</xs:element>
	<xs:element name="variations" type="xs:string"/>
	<xs:element name="summary" type="xs:string"/>
	<xs:element name="quantity" type="xs:integer"/>
	<xs:element name="charge_price" type="xs:decimal"/>
	<xs:element name="price_promotion" type="xs:decimal"/>
	<xs:element name="price_retail" type="xs:decimal"/>
	<xs:element name="price_wholesale" type="xs:decimal"/>
	<xs:element name="currency" type="xs:NCName"/>
	<xs:element name="code" type="xs:string"/>
	<xs:element name="created_at" type="xs:string"/>
	<xs:element name="updated_at" type="xs:string"/>
</xs:schema>